<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-20
 * Time: 11:25 AM
 */

namespace App\DB\Assist\Portal\User\Account;

use App\Assist\Encryptions\ConfirmEmailEncryption;
use App\Assist\Jwt\JWTAssistance;
use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Tables\Admin\AdminEmail;
use App\DB\Tables\Admin\AdminFailedLogin;
use App\DB\Tables\Admin\AdminLogs;
use App\DB\Tables\Admin\AdminPassword;
use App\DB\Tables\Cron\CronEmailRecord;
use Maatify\Json\Json;

class UserEmailConfirm extends AdminEmail
{
    public function ChangeEmail(): void
    {
        $password = $this->postValidator->Require('password', 'password');
        $email = $this->postValidator->Require('email', 'email');
        if (AdminPassword::obj()->Check(AdminLoginToken::obj()->GetAdminID(), $password)) {
            if (AdminLoginToken::obj()->GetAdminEmail() == $email) {
                Json::ErrorNoUpdate(__LINE__);
            } else {
                if($this->EmailIsExist($email)){
                    Json::Exist('email', line: __LINE__);
                }else{
                    $this->Set(AdminLoginToken::obj()->GetAdminID(), $email, AdminLoginToken::obj()->GetAdminName(), AdminLoginToken::obj()->GetAdminUsername());
                    AdminLogs::obj()->RecordMyLog('Change Email From: ' . AdminLoginToken::obj()->GetAdminUsername() . ' To: ' . $email);
                    $this->Success();
                }
            }
        } else {
            AdminFailedLogin::obj()->Failed(AdminLoginToken::obj()->GetAdminUsername());
            Json::Incorrect('credentials', line: __LINE__);
        }
    }

    public function EmailConfirm()
    {
        $code = $this->postValidator->Require('code', 'code');
        $this->Confirm($code);
        AdminLogs::obj()->RecordMyLog('Confirm Email');
        $this->Success();
    }

    public function EmailConfirmResend()
    {
        $this->ConfirmToken();
        AdminLogs::obj()->RecordMyLog('Email Confirm Resend');
        $this->Success();
    }

    private function Success(){
        Json::Success(AdminLoginToken::obj()->HandleAdminResponse(AdminLoginToken::obj()->ValidateAdminToken()));
    }


    /**
    ========================================= Confirm Code Hash =========================================
     **/
    private function Confirm(string $code): void
    {
        $admin_id = AdminLoginToken::obj()->GetAdminID();
        $username = AdminLoginToken::obj()->GetAdminUsername();
        if($row = $this->RowThisTable('*', '`admin_id` = ? ', [$admin_id])){
            if($row['confirmed']){
                Json::EmailAlreadyVerified(debug_backtrace()[0]['line']);
            }else{
                if(!empty($row['token']) && $code == $this->ConfirmCodeDecode($row['token'])){
                    $this->Edit(['confirmed'=> 1, 'token' => ''],"`admin_id` = ?", [$admin_id]);
                    AdminFailedLogin::obj()->Success($username);
                }else{
                    AdminFailedLogin::obj()->Failed($username);
                    Json::Incorrect('code', line: debug_backtrace()[0]['line']);
                }
            }
        }
    }

    private function ConfirmCodeDecode(string $code): string
    {
        $code = (new ConfirmEmailEncryption())->DeHashed($code);
        return (string) base64_decode($code);
    }

    private function ConfirmToken(): void
    {
        $admin_id = AdminLoginToken::obj()->GetAdminID();
        $name = AdminLoginToken::obj()->GetAdminName();
        $username = AdminLoginToken::obj()->GetAdminUsername();
        $email = AdminLoginToken::obj()->GetAdminEmail();
        if($admin = $this->RowThisTable('*', '`admin_id` = ?', [$admin_id])) {
            if(empty($admin['confirmed'])) {
                $this->RenewTokenAndSendEmail($admin_id, $username, $name, $email);
            }else{
                Json::EmailAlreadyVerified(line: debug_backtrace()[0]['line']);
            }
        }
    }

    private function Set(int $admin_id, string $email, string $name, string $username): void
    {
        if($this->Edit(['email'=>$email, 'confirmed'=>0], '`admin_id` = ?', [$admin_id])){
            $this->RenewTokenAndSendEmail($admin_id, $username, $name, $email);
        }
    }

    private function RenewTokenAndSendEmail(int $admin_id, string $username, string $name, string $email){
        $otp = $this->OTP();
        $this->Edit(['token' => $this->HashedOTP($otp)], '`admin_id` = ?', [$admin_id]);
        JWTAssistance::obj()->TokenConfirmMail($admin_id, $username);
        CronEmailRecord::obj()->RecordConfirmCode(0, $email, $otp, $name);
    }
}