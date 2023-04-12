<?php

namespace App\DB\Assist\Portal\User;

use App\DB\Tables\Admin\Admin;
use App\DB\Tables\Admin\Admin2FA;
use App\DB\Tables\Admin\AdminEmail;
use App\DB\Tables\Admin\AdminPrivileges;
use Maatify\Json\Json;

class Users extends Admin
{

    protected static int|string $line;

    private static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        self::$line = debug_backtrace()[0]['line'];
        return self::$instance;
    }

    public function __construct()
    {
        parent::__construct();
        self::$line = debug_backtrace()[0]['line'];
    }

    public function IdIsExist(int $admin_id): bool
    {
        return $this->RowIsExistThisTable('`id` = ? ', [$admin_id]);
    }

    public function UserForEdit(): array
    {
        if(
            (AdminPrivileges::obj()->IsMaster($this->admin_id) && !AdminPrivileges::obj()->IsMaster(AdminLoginToken::obj()->GetAdminID()))
           || $this->admin_id == AdminLoginToken::obj()->GetAdminID()
           || in_array($this->admin_id, [1,2])
        ){
            Json::Forbidden(self::$line);
        }else{
            $pre = $this->UsersTbsCols();
            $user = $this->Row(
                $pre['tb'],
                $pre['col'],
                "`$this->tableName`.`id` = ? GROUP BY `$this->tableName`.`id` ORDER BY `$this->tableName`.`id` ASC",
                [$this->admin_id]
            );
            if(!empty($user)){
                return $user;
            }else{
                Json::Incorrect('id', line: self::$line);
            }
        }
        return [];
    }

    protected function UsersTbsCols(): array
    {
        $tb_admin_emails = AdminEmail::obj()->TableName();
        $tb_admin_auth = Admin2FA::obj()->TableName();
        return ['tb'=>"`$this->tableName` 
            INNER JOIN `$tb_admin_emails` ON `$tb_admin_emails`.`admin_id` = `$this->tableName`.`id` 
            INNER JOIN `$tb_admin_auth` ON `$tb_admin_auth`.`admin_id` = `$this->tableName`.`id` 
            ",
                'col' => "`$this->tableName`.*, `$tb_admin_emails`.`email`, `$tb_admin_emails`.`confirmed`,  
            IF(`$tb_admin_auth`.`auth` = '', 0, 1) as auth,
            `$tb_admin_auth`.`isAuthRequired`"];

    }

    public function UsernameIsExist(string $username): string
    {
        return self::ColThisTable("`id`",
            "LCASE(`username`) = ? LIMIT 1 ",[strtolower($username)]);
    }

    public function AdminPrivileges(int $admin_id): array
    {
        if ($all = self::Rows("privilege_roles
            INNER JOIN `a_roles` ON `a_roles`.`role_id` = `privilege_roles`.`id`",
            "`privilege_roles`.`id`, `privilege_roles`.`r_name` as name, `privilege_roles`.`r_comment` as comment",
            " `a_roles`.`admin_id` = '$admin_id'")) {
            for ($i = 0; $i < sizeof($all); $i++) {
                $all[$i]['id'] = (int)$all[$i]['id'];
            }
            return $all;
        } else return [];
    }


}