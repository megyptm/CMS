<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 5:40 AM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\User\Privileges;

use App\DB\Assist\Portal\User\Users;
use App\DB\Tables\Admin\AdminLogs;
use App\DB\Tables\Admin\AdminRoles;
use App\DB\Tables\Privileges\PrivilegeRoles;
use Maatify\Json\Json;

class UserPrivilegeRoleRemove extends Users
{
    public function Do()
    {
        $this->PostedUserID();
        $role_id = (int)$this->postValidator->Require('role_id', 'int');
        if(!Users::obj()->IdIsExist($this->admin_id)){
            Json::Invalid('id', line: __LINE__);
        }elseif(!$role = PrivilegeRoles::obj()->ById($role_id)){
            Json::Invalid('role_id', line: __LINE__);
        }elseif(!AdminRoles::obj()->IsRoleExist($this->admin_id, $role_id)){
            Json::NotExist('role_id', line: __LINE__);
        }else{
            AdminRoles::obj()->Remove($this->admin_id, $role_id);
            AdminLogs::obj()->Record($this->admin_id,
                'RemovePrivilegeRoleFromUser' . PHP_EOL .
                ' Method: ' . $role['name_en'] . '-' . $role['name_ar'] . PHP_EOL .
                ' UserID: ' . $this->admin_id);
            Json::Success(PrivilegeRoles::obj()->AdminRoles($this->admin_id));
        }
    }
}