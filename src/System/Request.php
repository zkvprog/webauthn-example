<?php

namespace App\System;

class Request
{
    static public function getPath(): string
    {
        return trim(stristr($_SERVER['REQUEST_URI'], '?', true) ?: $_SERVER['REQUEST_URI'], '/');
    }

    static public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
}