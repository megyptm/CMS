<?php

namespace App\DB\Tables\Admin;

use App\Assist\Encryptions\ConfirmEmailEncryption;
use Maatify\DB\DbConnector;
use App\DB\Tables\Cron\CronEmailRecord;
use Maatify\Functions\GeneralFunctions;

class AdminEmail extends DbConnector
{
    protected string $tableName = 'a_email';

    private static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function EmailIsExist(string $email): string
    {
        return self::ColThisTable("`id`",
            "LCASE(`email`) = ? LIMIT 1 ",
            [strtolower($email)]
        );
    }

    public function SetUser(int $admin_id, string $email, string $name): void
    {
        if($this->Edit(['email'=>$email, 'confirmed'=>0], '`admin_id` = ?', [$admin_id])){
            $otp = $this->OTP();
            $this->Edit(['token' => $this->HashedOTP($otp)], '`admin_id` = ?', [$admin_id]);
            CronEmailRecord::obj()->RecordConfirmCode(0, $email, $otp, $name);
//            Mailer::obj()->ConfirmCode($name, $email, $otp);
        }
    }

    protected function OTP(): string
    {
        return GeneralFunctions::GenerateOTP(6);
    }

    protected function HashedOTP(string $otp): string
    {
        $code = base64_encode($otp);
        return (new ConfirmEmailEncryption())->Hash($code);
    }
}