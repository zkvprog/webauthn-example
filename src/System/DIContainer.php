<?php

namespace App\System;

use App\Exception\ApplicationException;
use App\System\Interfaces\ContainerInterface;

class DIContainer implements ContainerInterface
{
    public function __construct(private array $dependencies = [])
    {
    }

    public function get(string $key): object
    {
        if ($this->has($key)) {
            $dependency = $this->dependencies[$key];

            if (is_callable($dependency)) {
                return $dependency($this);
            }

            return $dependency;
        } else {
            throw new ApplicationException("Dependency '$key' not found");
        }
    }

    public function has(string $key): bool
    {
        return isset($this->dependencies[$key]);
    }
}