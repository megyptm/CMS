<?php

namespace App\DB\Tables\Admin;

use Maatify\DB\DbConnector;

class AdminToken extends DbConnector
{
    protected string $tableName = 'a_token';

    private static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }


}