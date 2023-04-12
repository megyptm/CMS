<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 6:02 AM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\Privileges\Roles;

use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Tables\Admin\AdminEditsLogs;
use App\DB\Tables\Privileges\PrivilegeMethods;
use App\DB\Tables\Privileges\PrivilegeRoles;
use App\DB\Tables\Privileges\Privileges;
use Maatify\Json\Json;

class AddNewRole extends PrivilegeRoles
{
    public function Do()
    {
        $name_ar = $this->postValidator->Require('name_ar', 'string');
        $name_en = $this->postValidator->Require('name_en', 'string');
        $comment = $this->postValidator->Optional('comment', 'string');
        if($this->NameExistEn($name_en)){
            Json::Exist('name_en', line: __LINE__);
        }
        if($this->NameExistAr($name_ar)){
            Json::Exist('name_ar', line: __LINE__);
        }
        $id = $this->Record($name_en, $name_ar, $comment);
        AdminEditsLogs::obj()->Record(
            AdminLoginToken::obj()->GetAdminID(), 'AddNewRole: ' . PHP_EOL .
                          ' name_en: ' . $name_en . PHP_EOL .
                          ' name_ar: ' . $name_ar . PHP_EOL .
                          ' description: ' . $comment
        );
        if($pages = PrivilegeMethods::obj()->AllIds()) {
            foreach ($pages as $method) {
                Privileges::obj()->Record($method['id'], $id);
            }
        }
        Json::Success();
    }
}