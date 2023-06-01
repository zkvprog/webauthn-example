<?php

namespace App\System;

class DIContainerFacade
{
    private static DIContainer $container;

    public static function setContainer(DIContainer $container)
    {
        self::$container = $container;
    }

    public static function get($key)
    {
        return self::$container->get($key);
    }
}