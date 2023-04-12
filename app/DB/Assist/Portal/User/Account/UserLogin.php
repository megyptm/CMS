<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-20
 * Time: 11:00 AM
 */

namespace App\DB\Assist\Portal\User\Account;

use App\Assist\Jwt\JWTAssistance;
use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Assist\Portal\User\Users;
use App\DB\Tables\Admin\Admin2FA;
use App\DB\Tables\Admin\AdminEmail;
use App\DB\Tables\Admin\AdminFailedLogin;
use App\DB\Tables\Admin\AdminLogs;
use App\DB\Tables\Admin\AdminPassword;
use App\DB\Tables\Admin\AdminPrivileges;
use Maatify\GoogleRecaptcha\V2\GoogleRecaptchaV2Json;
use Maatify\Json\Json;

class UserLogin extends Users
{
    public function Do()
    {
        (new GoogleRecaptchaV2Json());
        $username = $this->postValidator->Require('username', 'username');
        $password = $this->postValidator->Require('password', 'password');
        if($admin = $this->Login($username)){
            if (AdminPassword::obj()->Check($admin['id'], $password)) {
                unset($admin['password']);
                if ($admin['isActive'] == 1) {
                    $token = AdminLoginToken::obj()->GenerateToken($admin['id'], $admin['username']);
                    if ($admin['confirmed'] == 1 || empty($_ENV['EMAIL_CONFIRM_REQUIRED'])) {
                        if($_ENV['AUTH_2FA_STATUS']){
                            if($_ENV['AUTH_2FA_REQUIRED'] ||
                               AdminPrivileges::obj()->IsMaster($admin['id']) ||
                               $admin['isAdmin'] ||
                               $admin['isAuthRequired']){
                                Admin2FA::obj()->ResponseAuthMov($admin);
                            }else{
                                AdminLogs::obj()->RecordMyLog('success Login');
                                JWTAssistance::obj()->JwtTokenHash($admin['id'], $admin['username']);
                                AdminPassword::obj()->ValidateTempPass($admin['id']);
                                AdminFailedLogin::obj()->Success($admin['username']);
                            }
                        }else{
                            AdminLogs::obj()->RecordMyLog('success Login');
                            JWTAssistance::obj()->JwtTokenHash($admin['id'], $admin['username']);
                            AdminPassword::obj()->ValidateTempPass($admin['id']);
                            AdminFailedLogin::obj()->Success($admin['username']);
                        }
                        Json::Success(AdminLoginToken::obj()->HandleAdminResponse($admin));
                    }else{
                        JWTAssistance::obj()->TokenConfirmMail($admin['id'], $admin['username']);
                        Json::GoToMethod('EmailConfirm', 'Please Confirm Your Email',line: __LINE__);
                    }
                }else{
                    Json::SuspendedAccount();
                }
            }else {
                AdminFailedLogin::obj()->Failed($admin['username']);
                Json::Incorrect('credentials', line: __LINE__);
            }

        }else{
            AdminFailedLogin::obj()->Failed($username);
            Json::Incorrect('credentials', line: __LINE__);
        }
    }

    private function Login($username): array
    {
        $tb_email = AdminEmail::obj()->TableName();
        $tb_admin_auth = Admin2FA::obj()->TableName();

        return self::Row("`$this->tableName` 
        INNER JOIN `$tb_email` ON `$tb_email`.`admin_id` = `$this->tableName`.`id` 
        INNER JOIN `$tb_admin_auth` ON `$tb_admin_auth`.`admin_id` = `$this->tableName`.`id` 
        ",
            "`$this->tableName`.*, `$tb_email`.`email`, `$tb_email`.`confirmed`, `$tb_admin_auth`.`auth`, `$tb_admin_auth`.`isAuthRequired`",
            "LCASE(`$this->tableName`.`username`) = ? LIMIT 1 ",[strtolower($username)]);
    }
}