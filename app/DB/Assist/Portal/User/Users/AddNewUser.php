<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 5:23 AM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\User\Users;

use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Assist\Portal\User\Users;
use App\DB\Tables\Admin\Admin2FA;
use App\DB\Tables\Admin\AdminEmail;
use App\DB\Tables\Admin\AdminInfo;
use App\DB\Tables\Admin\AdminLogs;
use App\DB\Tables\Admin\AdminPassword;
use App\DB\Tables\Admin\AdminToken;
use Maatify\Functions\GeneralFunctions;
use Maatify\Json\Json;

class AddNewUser extends Users
{
    public function Do()
    {
        $email = $this->postValidator->Require('email', 'email');
        $username = $this->postValidator->Require('username', 'username');
        $name = $this->postValidator->Require('name', 'name');
        $user = $this->Register($username, $email, $name);
        Json::Success($user);
    }


    private function Register(string $username, string $email, string $name): array
    {
        if(AdminEmail::obj()->EmailIsExist($email)){
            Json::Exist('email');
        }
        if($this->UsernameIsExist($username)){
            Json::Exist('username');
        }
        if($this->admin_id = $this->Add([
            'username' => $username,
            'name' => $name,
            'isAdmin' => 0,
            'isActive' => 0,
        ])){
            $to_add = ['admin_id'=> $this->admin_id];
            Admin2FA::obj()->Add($to_add);
            AdminEmail::obj()->Add($to_add);
            AdminPassword::obj()->Add($to_add);
            AdminEmail::obj()->SetUser($this->admin_id, $email, $name);
            $otp = AdminPassword::obj()->SetTemp($this->admin_id, $name, $email);
            AdminToken::obj()->Add($to_add);
            AdminInfo::obj()->Add(['admin_id' => $this->admin_id, 'reg_date' => GeneralFunctions::CurrentDateTime(), 'reg_by' => AdminLoginToken::obj()->GetAdminID()]);
            AdminLogs::obj()->Record(
                $this->id,
                'Register: ' . PHP_EOL .
                ' Username: ' . $username . PHP_EOL .
                ' Name: ' . $name . PHP_EOL .
                ' Email: ' . $email
            );

            $user = $this->UserForEdit();
            $user['password'] = $otp;
            return $user;
        }
        return [];
    }
}