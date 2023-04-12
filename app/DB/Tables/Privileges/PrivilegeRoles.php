<?php

namespace App\DB\Tables\Privileges;

use App\DB\Tables\Admin\Admin;
use App\DB\Tables\Admin\AdminRoles;
use Maatify\DB\DbConnector;

class PrivilegeRoles extends DbConnector
{
    protected string $tableName = 'privilege_roles';

    private static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function AllIds(): array
    {
        return $this->RowsThisTable('`id`');
    }

    public function ById(int $id): array
    {
        return $this->RowThisTable('*', '`id` = ? ', [$id]);
    }

    public function NameExistEn(string $name): bool
    {
        return $this->ColExist('name_en', $name);
    }

    public function NameExistAr(string $name): bool
    {
        return $this->ColExist('name_ar', $name);
    }

    private function ColExist(string $col, string $val): bool
    {
        return $this->RowIsExistThisTable('`' . $col . '` = ?', [$val]);
    }

    public function Record(string $name_en, string $name_ar, string $comment): int
    {
        return $this->Add([
            'name_ar'=>$name_ar,
            'name_en'=>$name_en,
            'comment'=>$comment,
        ]);
    }

    public function Users(int $role_id): array
    {
        $tb_admin = Admin::obj()->TableName();
        $tb_admin_roles = AdminRoles::obj()->TableName();
        return $this->Rows("`$tb_admin`
        INNER JOIN `$tb_admin_roles` ON `$tb_admin_roles`.`admin_id` = `$tb_admin`.`id`",
            "`$tb_admin`.`id`, `$tb_admin`.`username`, `$tb_admin`.`name`, `$tb_admin`.`isActive`",
            "`$tb_admin_roles`.`role_id` = ?", [$role_id]);

    }

    public function AdminRoles(int $admin_id): array
    {
        $tb_privilege_roles = PrivilegeRoles::obj()->TableName();
        $tb_admin_roles = AdminRoles::obj()->TableName();
        return $this->PaginationHandler(
            // Count
            $this->CountTableRows("`$tb_admin_roles`", "id", "`$tb_admin_roles`.`admin_id` = ? ", [$admin_id]),

            // Data
            $this->PaginationRows("`$tb_privilege_roles` 
            INNER JOIN `$tb_admin_roles` ON `$tb_admin_roles`.`role_id` = `$tb_privilege_roles`.`id`",
                "`$tb_privilege_roles`.`id` as role_id, 
                `$tb_privilege_roles`.`name_en`, 
                `$tb_privilege_roles`.`name_ar`, 
                `$tb_privilege_roles`.`comment`",
                " `$tb_admin_roles`.`admin_id` = ? ",
                [$admin_id])
        );
    }
}