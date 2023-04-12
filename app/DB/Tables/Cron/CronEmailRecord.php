<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-02-27
 * Time: 4:25 PM
 */

namespace App\DB\Tables\Cron;


use App\Assist\Encryptions\ConfirmEmailEncryption;
use App\Assist\Encryptions\CronEmailEncryption;
use App\DB\Tables\Admin\AdminEmail;

class CronEmailRecord extends CronEmail
{
    private static self $instance;

    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function RecordMessage(int $ct_id,string $name, string $email, string $message, string $subject){
        $this->AddCron($ct_id, $name, $email, $message, $subject, 1);
    }

    public function RecordConfirmLink(int $ct_id,string $email, string $message)
    {
        $this->AddCron($ct_id, $email, $email, $message, 'Confirm Mail', 2);
    }

    public function RecordConfirmCode(int $ct_id,string $email, string $code, $name = '')
    {
        if(empty($name)){
            $name = $email;
        }

        $this->AddCron($ct_id, $name, $email, (new CronEmailEncryption)->Hash($code), 'Confirm Code', 3);
    }

    public function RecordTempPassword(int $ct_id,string $email, string $code, $name = '')
    {
        if(empty($name)){
            $name = $email;
        }

        $this->AddCron($ct_id, $name, $email, (new CronEmailEncryption)->Hash($code), 'Your Temporary Password', 4);
    }
}