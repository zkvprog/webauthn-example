<?php

namespace App\System\Interfaces;

interface ContainerInterface
{
    public function get(string $key);

    public function has(string $key);
}