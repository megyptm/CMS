<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-02-27
 * Time: 3:47 PM
 */

namespace App\DB\Tables\Cron;


use App\Assist\Encryptions\CronEmailEncryption;
use App\DB\Tables\Queue;
use Maatify\Functions\GeneralFunctions;
use Maatify\Mailer\Mailer;

class CronEmailSender extends CronEmail
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

    public function SentMarker(int $id){
        $this->Edit([
            'is_sent'     => 1,
            'sent_time'   => GeneralFunctions::CurrentDateTime(),
        ], '`id` = ? ', [$id]);
    }

    private function NotSent(): array
    {
        return $this->RowsThisTable('*', '`is_sent` = ? ', [0]);
    }

    public function CronSend()
    {
        Queue::obj()->Email();
        if($all = $this->NotSent()){
            foreach ($all as $item){
                $mailer = new Mailer($item['email'], $item['name']);
                $message = $item['message'];
                switch ($item['type']){
                    case 1;
                        $type = 'Message';
                        break;
                    case 2;
                        $type = 'ConfirmCustomerLink';
                        break;
                    case 3;
                        $type = 'ConfirmCode';
                        $message = (new CronEmailEncryption)->DeHashed($item['message']);
                        break;
                    case 4;
                        $type = 'TempPassword';
                        $message = ((new CronEmailEncryption))->DeHashed($item['message']);
                        break;
                    default;
                        $type = 'Message';
                }
                if($mailer->$type($message, $item['subject'])){
                    $this->SentMarker($item['id']);
                }
            }
        }
    }
}