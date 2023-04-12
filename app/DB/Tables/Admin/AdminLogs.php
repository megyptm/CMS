<?php

namespace App\DB\Tables\Admin;

use App\DB\Assist\Portal\User\AdminLoginToken;
use Maatify\DB\DbLogger;
use Maatify\Functions\GeneralFunctions;

class AdminLogs extends DbLogger
{
    protected string $tableName = 'a_logs';

    protected static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function RecordMyLog(string $description = ''): int
    {
        return $this->Record(AdminLoginToken::obj()->GetAdminID(), $description);
    }

    public function Record(int $admin_id, string $description): int
    {
        return $this->Add([
            'admin_id'    => $admin_id,
            'description' => $description,
            'user_id'     => AdminLoginToken::obj()->GetAdminID(),
            'time'        => GeneralFunctions::CurrentDateTime(),
            'ip'          => GeneralFunctions::IP(),
        ]);
    }

    public function AllLog(): array
    {
        return $this->ReturnHandler('admin_id');
    }

    public function UserLog(): array
    {
        $this->PostedUserID();
        return $this->ReturnHandler('admin_id');
    }

    public function OtherEditsLog(): array
    {
        $this->PostedUserID();
        return $this->ReturnHandler('log_by_id');
    }

    private function ReturnHandler(string $col): array
    {
        return $this->PaginationHandler(
            $this->Count($col),
            $this->Logs($col),
        );
    }

    private function Count(string $col): int
    {
        if(empty($this->admin_id)){
            $where = "`$col` > ?";
        }else{
            $where = "`$col` = ?";
        }
        return $this->CountThisTableRows('id', $where, [$this->admin_id]);
    }

    private function Logs(string $col): array
    {
        if(empty($this->admin_id)){
            $where = "`$this->tableName`.`id` > ?";
        }else{
            $where = "`$this->tableName`.`$col` = ?";
        }
        return $this->PaginationRows(
            "`$this->tableName` "
            ,
            "`$this->tableName`.`id`, `$this->tableName`.`admin_id` as user_id, `$this->tableName`.`description`, 
        `$this->tableName`.`time`, `$this->tableName`.`log_by_id`, `$this->tableName`.`log_by_name`",
            " $where ORDER BY `$this->tableName`.`id` DESC ", [$this->admin_id]
        );
    }
}