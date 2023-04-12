<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-02-16
 * Time: 9:23 AM
 */

namespace App\DB\Tables\Cron;


use Maatify\DB\DbConnector;
use Maatify\Functions\GeneralFunctions;

abstract class CronEmail extends DbConnector
{
    protected string $tableName = 'cron_email';

    protected array $cols = [
        'id'          => 1,
        'type'        => 1,
        'ct_id'       => 1,
        'name'        => 0,
        'email'       => 0,
        'message'     => 0,
        'subject'     => 0,
        'record_time' => 0,
        'is_sent'     => 1,
        'sent_time'   => 0,
    ];

    protected function AddCron(int $ct_id,string $name, string $email, string $message, string $subject, int $type = 1){
        $this->Add([
            'ct_id'       => $ct_id,
            'type'        => $type,
            'name'        => $name,
            'email'       => $email,
            'message'     => $message,
            'subject'     => $subject,
            'record_time' => GeneralFunctions::CurrentDateTime(),
            'is_sent'     => 0,
            'sent_time'   => GeneralFunctions::DefaultDateTime(),
        ]);
    }
}