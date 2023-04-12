<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-20
 * Time: 11:51 AM
 */

namespace App\DB\Assist\Portal\User\Account;


use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Tables\Admin\AdminFailedLogin;
use App\DB\Tables\Admin\AdminLogs;
use App\DB\Tables\Admin\AdminPassword;
use Maatify\Json\Json;

class UserPassword extends AdminPassword
{
    public function ChangePassword()
    {
        $password = $this->postValidator->Require('password', 'password');
        $password_old = $this->postValidator->Require('old_password', 'password');
        if ($password == $password_old) {
            Json::ErrorNoUpdate(__LINE__);
        } else {
            if ($this->Check(AdminLoginToken::obj()->GetAdminID(), $password_old)) {
                AdminLogs::obj()->RecordMyLog('Change Password');
                $this->Set(AdminLoginToken::obj()->GetAdminID(), $password);
                AdminLoginToken::obj()->LogoutSilent();
                Json::ReLogin(__LINE__);

            } else {
                AdminFailedLogin::obj()->Failed(AdminLoginToken::obj()->GetAdminUsername());
                Json::Incorrect('credentials', line: __LINE__);
            }
        }
    }
}