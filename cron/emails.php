<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-02-27
 * Time: 3:22 PM
 */

use App\DB\Tables\Cron\CronEmailSender;

require __DIR__ . '/../app/loader.php';

CronEmailSender::obj()->CronSend();