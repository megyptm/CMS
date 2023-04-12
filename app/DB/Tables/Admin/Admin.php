<?php

namespace App\DB\Tables\Admin;

use Maatify\DB\DbConnector;
use Maatify\Json\Json;

class Admin extends DbConnector
{
    protected string $tableName = 'admin';
    protected string $tableAlias = 'user';

    public const name = 'name';

    protected array $cols = [
        'id'       => 1,
        'username' => 0,
        'name'     => 0,
        'isAdmin'  => 1,
        'isActive' => 1,
    ];
    private static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function ExistID(int $admin_id): bool
    {
        return $this->ExistIDThisTable($admin_id);
    }

    public function GetAdminTitleName()
    {
        $this->PostedID();
        Json::Success([
            'id' => $this->id,
            'name'=>$this->ColThisTable("IFNULL(`name`, '')", "`id` = ? ", [$this->id])
        ]);
    }
}