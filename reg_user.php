<?php

require 'vendor/autoload.php';

use App\DbConnector;
use App\ResponseJson;

try {
    session_start();

    $pdo = DbConnector::getPdoInstance();
    $stmt = $pdo->prepare("INSERT INTO users (name, webauthn_id, password) VALUES(?, ?, ?)");
    $stmt->execute([$_POST['username'], bin2hex(random_bytes(16)), md5($_POST['password'])]);

    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['user_name'] = $_POST['username'];

    ResponseJson::sendSuccess('registration success');
} catch (Throwable $ex) {
    ResponseJson::sendError($ex->getMessage());
}