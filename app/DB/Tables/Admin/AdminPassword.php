<?php

namespace App\DB\Tables\Admin;

use App\Assist\Encryptions\AdminPasswordEncryption;
use App\DB\Tables\Cron\CronEmailRecord;
use Maatify\DB\DbConnector;
use Maatify\Functions\GeneralFunctions;
use Maatify\Json\Json;

class AdminPassword extends DbConnector
{

    protected string $tableName = 'a_pass';

    protected static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function Set(int $admin_id, string $password): bool
    {
        return $this->Edit(['password'=>$this->HashPassword($password), 'is_temp'=>0], '`admin_id` = ?', [$admin_id]);
    }

    public function SetTemp(int $admin_id, string $name, string $email): string
    {
        $otp = AdminPassword::obj()->DefaultPassword().'M';
        $this->Edit(['password'=>$this->HashPassword($otp), 'is_temp'=>1], '`admin_id` = ?', [$admin_id]);
        if(!empty($email)) {
//            Mailer::obj()->TempPassword($name, $email, $otp);
            CronEmailRecord::obj()->RecordTempPassword(0, $email, $otp, $name);
        }
        return $otp;
    }

    public function SetAllMaster(string $password): bool
    {
        return $this->Edit(['password'=>$this->HashPassword($password)], '`admin_id` <= ?', [AdminPrivileges::obj()->MasterIds()]);
    }

    public function Check(int $admin_id, string $password): string
    {
        if($a_pass = $this->ColThisTable('`password`', '`admin_id` = ?', [$admin_id])){
            return $this->CheckPassword($password, $a_pass);
        }
        return '';
    }

    private function PasswordSecretKeyEncode($code): string
    {
        $code = base64_encode($code);
        return (new AdminPasswordEncryption())->Hash($code);
    }
    private function PasswordSecretKeyDecode($code): string
    {
        $code = (new AdminPasswordEncryption())->DeHashed($code);
        return (string)base64_decode($code);
    }
    public function HashPassword($password): string
    {
        return self::PasswordSecretKeyEncode(password_hash($password, PASSWORD_DEFAULT));
    }
    private function CheckPassword($password, $hashedPassword): bool
    {
        return password_verify($password, self::PasswordSecretKeyDecode($hashedPassword));
    }

    public function ValidateTempPass(int $admin_id)
    {
        if($this->ColThisTable('is_temp', '`admin_id` = ? AND `is_temp` = ?', [$admin_id, 1])){
            Json::GoToMethod('ChangePassword', line: debug_backtrace()[0]['line']);
        }
    }

    public function DefaultPassword(): string
    {
        return GeneralFunctions::GenerateOTP(8);
    }
}