<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 4:19 PM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\Privileges\Roles;

use App\DB\Tables\Privileges\PrivilegeRoles;
use App\DB\Tables\Privileges\Privileges;
use Maatify\Json\Json;

class MethodsOfRoleID extends PrivilegeRoles
{

    public function Get()
    {
        $this->PostedID();
        if(!$row = $this->ById($this->id)){
            Json::Invalid('id', line: __LINE__);
        }else{
            $row['privileges'] = Privileges::obj()->ByRole($this->id);
        }
        Json::Success($row);
    }
}