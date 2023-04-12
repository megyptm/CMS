<?php

namespace App\DB\Tables\Privileges;


use Maatify\DB\DbConnector;

class Privileges extends DbConnector
{
    protected string $tableName = 'privileges';
    private string $tb_privilege_roles;
    private string $tb_privilege_methods;

    public function __construct()
    {
        parent::__construct();
        $this->tb_privilege_roles = PrivilegeRoles::obj()->TableName();
        $this->tb_privilege_methods = PrivilegeMethods::obj()->TableName();
    }

    private static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function Record(int $method_id, int $role_id): int
    {
        return $this->Add(['method_id' => $method_id,
                    'role_id' => $role_id,
                    'granted' => 0,
        ]);
    }

    public function ById(int $id): array
    {
        return $this->RowThisTable('*', '`id` = ? ', [$id]);
    }

    public function ByMethod(int $method_id): array
    {
        return $this->Rows(
            "`$this->tableName` INNER JOIN `$this->tb_privilege_roles` ON `$this->tb_privilege_roles`.`id`= `privileges`.`role_id`",
            "`$this->tableName`.`id` as privilege_id, `$this->tableName`.`granted`, 
            `$this->tb_privilege_roles`.`name_en` as role, `$this->tb_privilege_roles`.`comment` as comment",
            "`$this->tableName`.`method_id` = ? ",
            [$method_id]
        );
    }

    public function ByRole(int $method_id): array
    {
        return $this->Rows(
            "`$this->tableName` 
            INNER JOIN `$this->tb_privilege_methods` ON `$this->tb_privilege_methods`.`id` = `$this->tableName`.`method_id`",
            "`$this->tableName`.`id` as privilege_id, `$this->tableName`.`granted`, 
            `$this->tb_privilege_methods`.`id` as method_id, `$this->tb_privilege_methods`.`method`, 
            `$this->tb_privilege_methods`.`name_ar`, `$this->tb_privilege_methods`.`name_en`, 
            `$this->tb_privilege_methods`.`comment`",
            "`$this->tableName`.`role_id` = ? ",
            [$method_id]
        );
    }
}