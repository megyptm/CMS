<?php

namespace App\DB\Tables\Admin;


use Maatify\DB\DbConnector;

class AdminRoles extends DbConnector
{
    protected string $tableName = 'a_roles';

    private static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function Record(int $admin_id, int $role_id): int
    {
        return $this->Add([
            'admin_id'=>$admin_id,
            'role_id'=>$role_id,
        ]);
    }

    public function Remove(int $admin_id, int $role_id): int
    {
        return $this->Delete('`admin_id` = ? AND `role_id` = ? ', [$admin_id, $role_id]);
    }

    public function IsRoleExist(int $admin_id, int $role_id): bool
    {
        return $this->RowIsExistThisTable("`admin_id` = ? AND `role_id` = ?", [$admin_id, $role_id]);
    }
}