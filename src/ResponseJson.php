<?php

namespace App;

class ResponseJson
{
    public static function sendSuccess(string $msg = '', $body = false)
    {
        self::send(true, $msg, $body);
    }

    public static function sendError(string $msg = '', $body = false)
    {
        self::send(false, $msg, $body);
    }

    public static function send($success = false, $msg = '', $body = false)
    {
        if (!$body) {
            $return = new \stdClass();
            $return->success = $success;
            $return->msg = $msg;

            header('Content-Type: application/json');
            print(json_encode($return));
        } else {
            if (!self::isJson($body)) {
                $body = json_encode($body);
            }

            header('Content-Type: application/json');
            print($body);
        }
    }

    public static function isJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}