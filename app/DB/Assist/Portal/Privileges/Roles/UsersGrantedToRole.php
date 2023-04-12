<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 4:25 PM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\Privileges\Roles;

use App\DB\Tables\Privileges\PrivilegeRoles;
use Maatify\Json\Json;

class UsersGrantedToRole extends PrivilegeRoles
{
    public function Get()
    {
        $this->PostedID();
        if(!$rowPrivileges = $this->ById($this->id)){
            Json::Invalid('id', line: __LINE__);
        }else{
            Json::Success(['name_en' => $rowPrivileges['name_en'], 'name_ar' => $rowPrivileges['name_ar'], 'users' =>$this->Users($this->id)]);
        }
    }
}