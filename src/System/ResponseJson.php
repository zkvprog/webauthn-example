<?php

namespace App\System;

use App\System\Interfaces\JsonResponseInterface;
use JsonSerializable;

class ResponseJson implements JsonResponseInterface, JsonSerializable
{
    public function __construct(public bool $success = false, public string $message = '', public array|\stdClass $result = [])
    {
    }

    public function sendResponse()
    {
        header('Content-Type: application/json');
        print(json_encode($this));
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}