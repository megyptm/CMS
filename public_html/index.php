<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-04-10
 * Time: 4:13 AM
 * https://www.Maatify.dev
 */

use Maatify\App\App;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__) );
$dotenv->load();
new App();

function site_url(): string
{
    return 'https://' . $_SERVER['HTTP_HOST'];
    //    return 'https://www.howmanyhours.com';
}