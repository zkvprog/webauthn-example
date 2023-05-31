<?php

namespace App\Db;

class PdoConnector implements DbConnector
{
    public static function getPdoInstance()
    {
        $dsn = "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABSE']};charset=utf8";

        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ];

        return new \PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $options);
    }
}