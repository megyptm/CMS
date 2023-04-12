<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 4:14 PM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\Privileges\Methods;

use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Tables\Admin\AdminEditsLogs;
use App\DB\Tables\Privileges\PrivilegeMethods;
use Maatify\Json\Json;

class UpdateMethod extends PrivilegeMethods
{

    public function Do()
    {
        $this->PostedID();
        $method = $this->postValidator->Require('method', 'string');
        $name_ar = $this->postValidator->Require('name_ar', 'string');
        $name_en = $this->postValidator->Require('name_en', 'string');
        $comment = $this->postValidator->Optional('comment', 'string');
        if(!$row = $this->ById($this->id)){
            Json::Invalid('id', line: __LINE__);
        }else{
            $edits = array();
            $log = '';
            if(!empty($method) && $method <> $row['method']){
                if($this->MethodExist($method)){
                    Json::Exist('method', line: __LINE__);
                }else{
                    $edits['method'] = $method;
                    $log .= ' Method: ' . $row['method'] . ' to: ' . $method . PHP_EOL;
                }
            }
            if(!empty($name_ar) && $name_ar <> $row['name_ar']){
                if($this->NameExistAr($name_ar)){
                    Json::Exist('name_ar', line: __LINE__);
                }else{
                    $edits['name_ar'] = $name_ar;
                    $log .= ' NameAr: ' . $row['name_ar'] . ' to: ' . $name_ar . PHP_EOL;
                }
            }
            if(!empty($name_en) && $name_en <> $row['name_en']){
                if($this->NameExistEn($name_en)){
                    Json::Exist('name_en', line: __LINE__);
                }else{
                    $edits['name_en'] = $name_en;
                    $log .= ' NameEn: ' . $row['name_en'] . ' to: ' . $name_en . PHP_EOL;
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
                AdminEditsLogs::obj()->Record(
                    AdminLoginToken::obj()->GetAdminID(), 'UpdateRoleMethod: ' . PHP_EOL . $log);
            }
        }
        Json::Success();
    }
}