<?php

namespace App\Assist;

use Maatify\Json\Json;
use Maatify\Validation\PostValidatorHandler;

class PostValidator extends PostValidatorHandler
{

    private static self $instance;

    public static function obj(): self
    {
        if(!isset(self::$instance))
        {
            self::$instance = new self();
        }
        self::$line = debug_backtrace()[0]['line'];
        return self::$instance;
    }
    protected function HandlePostType(string $name, string $type, string $more_info = ''): string
    {
        switch ($type){
            case 'email';
                return self::EmailValidation($_POST[$name], $name);
            case 'ip';
                return self::IPValidation($_POST[$name], $name);
            case 'phone';
                return self::PhoneValidation($name, $type ,$more_info);
            case 'date':
            case 'year':
            case 'year_month':
            case 'name';
            case 'username';
            case 'password';
            case 'account_no';
            case 'national_id';
            case 'pin';
            case 'code';
            case 'app_type';
            case 'name_en';
            case 'name_ar';
                if(!preg_match(self::Patterns($type), $_POST[$name])){
                    Json::Invalid($name, $more_info, self::$line);
                    exit();
                }
                break;
            case 'day';
            case 'month';
                if(!is_numeric($_POST[$name]) && $_POST[$name] <= 0){
                    Json::Invalid($name,$more_info, self::$line);
                    exit();
                }else{
                    if($_POST[$name] > 9){
                        if(!preg_match(self::Patterns($type), $_POST[$name])){
                            Json::Invalid($name, $more_info, self::$line);
                            exit();
                        }
                    }else{
                        return '0'.$_POST[$name];
                    }
                }
                break;

            case 'float':
            case 'int';
                if(!is_numeric($_POST[$name])){
                    Json::Invalid($name,$more_info, self::$line);
                    exit();
                }
                break;
        }
        return self::ClearInput($_POST[$name]);
    }

    private function Patterns(string $typeName): string
    {
        return match ($typeName) {
            //            'phone' => '/^\+[0-9]?()[0-9](\s|\S)(\d[0-9]{8,10})$/',
            //            'phone' => '/^\+\d?()\d(\s|\S)(\d\d{8,10})$/',
            //            'name' => '/^[a-zA-Z\s]*$/i',
            'name' => '/^[\p{Arabic}a-zA-Z_\-\s\d]*$/iu',
            'name_en' => '/^[a-zA-Z_\-\s]*$/i',
            'name_ar' => '/^[\p{Arabic}a-zA-Z_\-\s\d]*$/iu',
            'username' => '/^[a-zA-Z0-9]*$/i',
            'phone' => '/^\d*$/i',
            'phone_full' => '/^\+\d*$/i',
            //            'date' => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/',
            'year' => '/^(19[0-9][0-9]|2[0-1][0-9][0-9])$/',
            'month' => '/^((0[1-9]|1[0-2]))$/',
            'day' => '/^(0[1-9]|[1-2][0-9]|3[0-1])$/',
            'year_month' => '/^(19[0-9][0-9]|2[0-1][0-9][0-9])-(0[1-9]|1[0-2])$/',
            'date' => '/^(19[0-9][0-9]|2[0-1][0-9][0-9])-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/',
            'datetime' => '/^(19[0-9][0-9]|2[0-1][0-9][0-9])-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/',
            //            'password' => '/^(?=.*\d)(?=.*[a-zA-Z]).{7,70}$/',
            'password' => '/^(?=.*\d)(?=.*[a-zA-Z])[0-9A-Za-z!@#$%+_\-&]{7,70}$/',
            'account_no' => '/^[0-9]{9}$/',
            'national_id' => '/^[0-9]{14}$/',
            'pin', 'code' => '/^[0-9]{6}$/',
            'app_type' => '/^[1-3]{1}$/',
            'int' => '/^[0-9]+$/i',
            'float' => '/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/',
            'bool' => '/^[0-1]{1}$/',
            default => '',
        };
    }
}