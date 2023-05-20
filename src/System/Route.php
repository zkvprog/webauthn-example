<?php

namespace App\System;

use Closure;

class Route
{
    private string $httpMethod;
    private string $path;
    private Closure $callback;

    public function __construct(string $httpMethod, string $path, array $callback)
    {
        $this->httpMethod   = $httpMethod;
        $this->path     = $path;
        $this->callback = $this->prepareCallback($callback);
    }

    private function prepareCallback(array $callback): Closure
    {
        return function (...$params) use ($callback) {
            list($class, $method) = $callback;
            return (new $class)->{$method}(...$params);
        };
    }

    public function match(string $uri, string $method): bool
    {
        return preg_match('/^' . str_replace(['*', '/'], ['\w+', '\/'], $this->path) . '$/', $uri)
                    && $method === $this->httpMethod;
    }

    public function run(string $uri)
    {
        $pathArr = explode('/', $uri);
        $intersect = array_intersect($pathArr, explode('/', $this->path));

        return call_user_func_array($this->callback, array_diff($pathArr, $intersect));
    }
}
