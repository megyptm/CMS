<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-02-16
 * Time: 10:01 AM
 */


use Maatify\App\App;

date_default_timezone_set('Africa/Cairo');
//date_default_timezone_set('Europe/Paris');

session_start();

if(empty($_POST)){
    $response = file_get_contents('php://input');
    $response2 = json_decode($response, true);
    $_POST = json_decode(json_encode($response2), true);
}
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
new App();