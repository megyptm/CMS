<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 3:29 AM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\User\Users;

use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Assist\Portal\User\Users;
use App\DB\Tables\Admin\AdminEmail;
use App\DB\Tables\Admin\AdminLogs;
use Maatify\Json\Json;

class UpdateUserInfo extends Users
{

    public function Do()
    {
        $this->PostedUserID();
        $user = $this->UserForEdit();
        $email = $this->postValidator->Optional('email', 'email');
        $username = $this->postValidator->Optional('username', 'username');
        $name = $this->postValidator->Optional('name', 'name');
        $edits = array();
        $logs = '';
        if(empty($email) && empty($username) && empty($name)){
            Json::ErrorNoUpdate(__LINE__);
        }else{
            if (! empty($email) && $email != $user['email']) {
                if(AdminEmail::obj()->EmailIsExist($email)) {
                    Json::Exist('email', line: __LINE__);
                }else{
                    AdminEmail::obj()->SetUser($this->admin_id, $email, $user['name']);
                    $logs .= 'Email: ' . $user['email'] . ' to ' . $email . PHP_EOL;
                }
            }
            if (! empty($username) && $username != $user['username']) {
                if(Users::obj()->UsernameIsExist($username)){
                    Json::Exist('username', line: __LINE__);
                }else{
                    $edits['username'] = $username;
                    $logs .= 'Username: ' . $user['username'] . ' to ' . $username . PHP_EOL;
                }
            }
            if (! empty($name) && $name != $user['name']) {
                $edits['name'] = $name;
                $logs .= 'Name: ' . $user['name'] . ' to ' . $name . PHP_EOL;
            }
            if (! empty($logs)) {
                AdminLogs::obj()->Record($this->admin_id, 'Update: ' . PHP_EOL . $logs);
            } else {
                Json::ErrorNoUpdate(__LINE__);
            }
            if (! empty($edits)) {
                $this->Edit($edits, '`id` = ? ', [$this->admin_id]);
            }
            Json::Success($this->UserForEdit());
        }
    }
}