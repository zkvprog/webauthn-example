<?php

namespace App;

class DbConnector
{
    public static function getPdoInstance()
    {
        $dsn = "mysql:host=localhost;port=3306;dbname=passkey;charset=utf8";
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ];

        return new \PDO($dsn, 'root', '', $options);
    }
}