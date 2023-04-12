<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 4:07 PM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\Privileges\Methods;

use App\DB\Tables\Privileges\PrivilegeMethods;
use Maatify\Json\Json;

class AllMethods extends PrivilegeMethods
{

    public function Get()
    {

        Json::Success($this->All());
    }


    public function All(): array
    {
        $where = "`id` > ? ";
        return $this->PaginationHandler($this->CountThisTableRows('`id`', $where, [$this->id]),
            $this->PaginationRows($this->tableName, '*', $where . ' ORDER BY `sort` DESC ', [$this->id]));
    }
}