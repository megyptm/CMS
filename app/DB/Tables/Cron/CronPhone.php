<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-02-16
 * Time: 11:56 AM
 */

namespace App\DB\Tables\Cron;

use Maatify\DB\DbConnector;
use Maatify\Functions\GeneralFunctions;

class CronPhone extends DbConnector
{
    protected string $tableName = 'cron_phone';

    protected array $cols = [
        'id'          => 1,
        'ct_id'       => 1,
        'phone'       => 0,
        'message'     => 0,
        'record_time' => 0,
        'is_sent'     => 1,
        'sent_time'   => 0,
    ];
    private static self $instance;

    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function Record(int $ct_id, string $phone, string $message, int $type = 1){
        $this->Add([
            'ct_id'       => $ct_id,
            'type'        => $type,
            'phone'       => $phone,
            'message'     => $message,
            'record_time' => GeneralFunctions::CurrentDateTime(),
            'is_sent'     => 0,
        ]);
    }

    public function SentMarker(int $id){
        $this->Edit([
            'is_sent'     => 1,
            'sent_time'   => GeneralFunctions::CurrentDateTime(),
        ], '`id` = ? ', [$id]);
    }

    public function NotSent(): array
    {
        return $this->RowsThisTable('*', '`is_sent` = ? ', [0]);
    }

    public function CronSend()
    {

        // prepare sms sender
        if($all = $this->NotSent()){
            foreach ($all as $item){
                if(SmsSender::obj()->SendSms($item['phone'], $item['message'])){
                    $this->SentMarker($item['id']);
                }
            }
        }
    }
}