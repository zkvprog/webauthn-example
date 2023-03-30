<?php

require 'vendor/autoload.php';

use App\ResponseJson;

try {
    session_start();
    session_destroy();

    ResponseJson::sendSuccess('logout success');
} catch (Throwable $ex) {
    ResponseJson::sendError($ex->getMessage());
}