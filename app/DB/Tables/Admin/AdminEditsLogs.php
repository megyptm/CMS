<?php

namespace App\DB\Tables\Admin;

use Maatify\DB\DbLogger;
use Maatify\Functions\GeneralFunctions;

class AdminEditsLogs extends DbLogger
{
//    use PaginationTrait;
    protected string $tableName = 'a_edits_log';

    protected static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function Record(int $admin_id, string $description): int
    {
        return $this->Add([
            'admin_id'    => $admin_id,
            'type'        => GeneralFunctions::CurrentPage(),
            'description' => $description,
            'time'        => GeneralFunctions::CurrentDateTime(),
            'ip'          => GeneralFunctions::IP(),
        ]);
    }

    public function AllLog(): array
    {
        return $this->ReturnHandler();
    }

    public function UserLog(): array
    {
        $this->PostedUserID();
        return $this->ReturnHandler();
    }

    private function ReturnHandler(): array
    {
        return $this->PaginationHandler(
            $this->Count(),
            $this->Pagination()
        );
    }

    private function Count(): int
    {
        if(empty($this->admin_id)){
            $where = "`id` > ?";
        }else{
            $where = "`admin_id` = ?";
        }
        return $this->CountThisTableRows('id', $where, [$this->admin_id]);
    }

    private function Pagination(): array
    {
        if(empty($this->admin_id)){
            $where = "`$this->tableName`.`id` > ?";
        }else{
            $where = "`$this->tableName`.`admin_id` = ?";
        }
        return $this->Rows("`$this->tableName` "
            ,
            "`$this->tableName`.`id`, `$this->tableName`.`admin_id` as user_id, `$this->tableName`.`description`, 
        `$this->tableName`.`time`",
            " $where ORDER BY `$this->tableName`.`id` DESC " . $this->AddWherePagination() , [$this->admin_id]);
    }
}