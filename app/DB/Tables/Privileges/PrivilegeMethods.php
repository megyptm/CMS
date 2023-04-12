<?php

namespace App\DB\Tables\Privileges;


use Maatify\DB\DbConnector;

class PrivilegeMethods extends DbConnector
{
    protected string $tableName = 'privilege_methods';

    private static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function Record(string $page, string $name_ar, string $name_en, string $comment): int
    {
        return $this->Add([
            'method'  => $page,
            'sort'    => $this->MaxIDThisTable()+1,
            'name_ar' => $name_ar,
            'name_en' => $name_en,
            'comment' => $comment,
        ]);
    }

    public function AllIds(): array
    {
        return $this->RowsThisTable('`id`');
    }

    public function ById(int $id): array
    {
        return $this->RowThisTable('*', '`id` = ? ', [$id]);
    }

    public function MethodExist(string $name): bool
    {
        return $this->ColExist('method', $name);
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


}