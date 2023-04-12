<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 5:32 AM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\User\Privileges;

use App\DB\Assist\Portal\User\Users;
use App\DB\Tables\Privileges\PrivilegeRoles;
use Maatify\Json\Json;

class UserPrivileges extends Users
{
    public function Get()
    {
        $this->PostedUserID();
        if(!Users::obj()->IdIsExist($this->admin_id)){
            Json::Invalid('id', line: __LINE__);
        }else{
            Json::Success(PrivilegeRoles::obj()->AdminRoles($this->admin_id));
        }
    }
}