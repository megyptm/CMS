<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 4:10 PM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\Privileges\Methods;

use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Tables\Admin\AdminEditsLogs;
use App\DB\Tables\Privileges\PrivilegeMethods;
use App\DB\Tables\Privileges\PrivilegeRoles;
use App\DB\Tables\Privileges\Privileges;
use Maatify\Json\Json;

class AddNewMethod extends PrivilegeMethods
{

    public function Do()
    {
        $method = $this->postValidator->Require('method', 'string');
        $name_ar = $this->postValidator->Require('name_ar', 'string');
        $name_en = $this->postValidator->Require('name_en', 'string');
        $comment = $this->postValidator->Optional('comment', 'string');
        if($this->MethodExist($method)){
            Json::Exist('method', line: __LINE__);
        }
        if($this->NameExistEn($name_en)){
            Json::Exist('name_en', line: __LINE__);
        }
        if($this->NameExistAr($name_ar)){
            Json::Exist('name_ar', line: __LINE__);
        }
        $id = $this->Record($method, $name_ar, $name_en, $comment);
        if($roles = PrivilegeRoles::obj()->AllIds()){
            foreach ($roles as $role){
                Privileges::obj()->Record($id, $role['id']);
            }
        }
        AdminEditsLogs::obj()->Record(AdminLoginToken::obj()->GetAdminID(),
            'AddNewRoleMethod: ' . PHP_EOL .
            ' Method: ' . $method . PHP_EOL .
            ' MethodNameAr: ' . $name_ar . PHP_EOL .
            ' MethodNameEn: ' . $name_en . PHP_EOL .
            ' Description: ' . $comment . PHP_EOL
        );
        Json::Success();
    }
}