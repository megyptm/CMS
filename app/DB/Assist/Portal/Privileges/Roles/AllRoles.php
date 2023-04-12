<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 5:53 AM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\Privileges\Roles;

use App\DB\Tables\Privileges\PrivilegeRoles;
use Maatify\Json\Json;

class AllRoles extends PrivilegeRoles
{
    public function Get()
    {
        Json::Success($this->AllRoles());
    }

    private function AllRoles(): array
    {
        return $this->PaginationHandler($this->CountThisTableRows('`id`', '`id` > ? ', [$this->id]),
            $this->PaginationRows($this->tableName, '*', '`id` > ? ORDER BY `id` DESC ', [$this->id]));
    }
}