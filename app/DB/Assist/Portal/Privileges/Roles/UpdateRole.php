<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 6:05 AM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\Privileges\Roles;

use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Tables\Admin\AdminEditsLogs;
use App\DB\Tables\Privileges\PrivilegeRoles;
use Maatify\Json\Json;

class UpdateRole extends PrivilegeRoles
{
    public function Do()
    {
        $this->PostedID();
        $name_en = $this->postValidator->Optional('name_en', 'string');
        $name_ar = $this->postValidator->Optional('name_ar', 'string');
        $comment = $this->postValidator->Optional('comment', 'string');
        if(!$row = $this->ById($this->id)){
            Json::Invalid('id', line: __LINE__);
        }else{
            $edits = array();
            $log = '';
            if(!empty($name_en) && $name_en <> $row['name_en']){
                if($this->NameExistEn($name_en)){
                    Json::Exist('name_en', line: __LINE__);
                }else{
                    $edits['name_en'] = $name_en;
                    $log .= ' NameEn: ' . $row['name_en'] . ' to: ' . $name_en . PHP_EOL;
                }
            }
            if(!empty($name_ar) && $name_ar <> $row['name_ar']){
                if($this->NameExistEn($name_ar)){
                    Json::Exist('name_ar', line: __LINE__);
                }else{
                    $edits['name_ar'] = $name_ar;
                    $log .= ' NameAR: ' . $row['name_ar'] . ' to: ' . $name_en . PHP_EOL;
                }
            }
            if(!empty($comment) && $comment <> $row['comment']){
                $edits['comment'] = $comment;
                $log .= ' Description: ' . $row['comment'] . ' to: ' . $comment . PHP_EOL;
            }
            if(empty($edits)){
                Json::ErrorNoUpdate(__LINE__);
            }else{
                $this->Edit($edits, '`id` = ? ', [$this->id]);
                $log .= ' ID: ' . $this->id;
                AdminEditsLogs::obj()->Record(AdminLoginToken::obj()->GetAdminID(), 'UpdateRole: ' . PHP_EOL . $log);
            }
        }
        Json::Success();
    }
}