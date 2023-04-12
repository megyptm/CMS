<?php

namespace App\DB\Tables\Admin;

use App\DB\Assist\Portal\User\AdminLoginToken;
use Maatify\DB\DbConnector;
use Maatify\Functions\GeneralFunctions;
use Maatify\Json\Json;

class AdminFailedLogin extends DbConnector
{

    protected string $tableName = 'a_f_login';

    private string $ip;

    public function __construct()
    {
        parent::__construct();
        $this->ip = GeneralFunctions::IP();
    }

    protected static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function CheckFailed(){
        $this->IsFailedRecord();
    }

    private function IsFailed(string $ip = ''): int
    {
        //        $time = date("Y-m-d H:i:s", strtotime('-1 days')); // -1 hours
        if(empty($ip)){
            $ip = $this->ip;
        }

        return $this->CountTableRows("`$this->tableName`
			LEFT JOIN `$this->tableName` as success
            	ON `success`.`isSuccess` AND
                	`success`.`ip` = '$ip' AND
                	`success`.`id` = (select `id` FROM `$this->tableName`
                                      WHERE `isSuccess` AND `id` = `$this->tableName`.`id` AND `ip` = '$ip' ORDER BY `id` DESC LIMIT 1)

            LEFT JOIN `$this->tableName` AS afl
            	ON `afl`.`id` > ifnull(`success`.`id`,0) AND
                `afl`.`ip` = '$ip'",
            '`afl`.`id`',
            "`a_f_login`.`ip` = '$ip' 
            	GROUP By `$this->tableName`.`id`
                ORDER BY `$this->tableName`.`id` DESC LIMIT 1");
    }

    private function IsFailedRecord(string $ip = ''): int
    {
        $check = $this->IsFailed($ip);
        if(!empty($check)){
            if($check >= $this->Tries()){
                Json::UnauthorizedBlock();
                exit();
            }else{
                return $check;
            }
        }else{
            return 0;
        }
    }

    private function Tries(): int
    {
        return 1000;
    }

    public function SuccessByAdmin(string $ip, string $username): bool
    {
        if($this->IsFailedRecord($ip)){
            return $this->Record(1, $ip, $username,AdminLoginToken::obj()->GetAdminID());
        }else{
            Json::NotExist('ip', line: debug_backtrace()[0]['line']);
        }
        return false;
    }

    public function Failed(string $username): bool
    {
        $check = $this->IsFailedRecord();
        if($check < $this->Tries()){
            return $this->Record(0, $this->ip, $username, 0);
        }
        return false;
    }

    public function Success($username): bool
    {

        if($this->IsFailedRecord()){
            return $this->Record(1, $this->ip, $username, 0);
        }
        return false;
    }

    private function Record(int $is_success, string $ip, string $username, int $admin_id): int
    {
        return $this->Add([
            'isSuccess' => $is_success,
            'ip' => $ip,
            'username' => strtolower($username),
            'time' => GeneralFunctions::CurrentDateTime(),
            'page' => GeneralFunctions::CurrentPage() . (!empty($_GET['action']) ? '/' . $_GET['action'] : ''),
            'admin_id'=>$admin_id
        ]);
    }

    public function Log(string $username, string $ip): array
    {
        $where = $this->PrepareWhere($username, $ip);
        $tb_admin = Admin::obj()->TableName();
        return $this->PaginationHandler(
            // Count
            $this->CountThisTableRows(
                'id',
                "$where"),

            // Filter
            $this->PaginationRows("`$this->tableName`
                  LEFT JOIN `$tb_admin` ON `$tb_admin`.`id` = `$this->tableName`.`admin_id` ",
            " `$this->tableName`.`id`, `$this->tableName`.`isSuccess`, `$this->tableName`.`ip`, `$this->tableName`.`username`, `$this->tableName`.`time`,
         COALESCE(`$tb_admin`.`name`, 'User') as log_by ",
            " $where ORDER BY `$this->tableName`.`id` DESC "),
        );
    }

    private function PrepareWhere(string $username, string $ip): string
    {
        $where = "";
        if(!empty($username)){
            $where .= " LCASE(`$this->tableName`.`username`) = LCASE('$username') OR ";
        }
        if(!empty($ip)){
            $where .= " `$this->tableName`.`ip` = '$ip' OR ";
        }
        $where = rtrim($where, " OR ");

        if(empty($where)){
            $where = " `$this->tableName`.`id` > '0' ";
        }
        return $where;
    }
}