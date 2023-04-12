<?php

namespace App\DB\Tables\Admin;

use Maatify\DB\DbConnector;

class AdminInfo extends DbConnector
{
    protected string $tableName = 'a_info';

    protected static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }
}