<?php

require 'vendor/autoload.php';

use App\DbConnector;
use App\ResponseJson;

try {
    session_start();

    $pdo = DbConnector::getPdoInstance();
    $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE name=?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();

    if (isset($user)) {
        if (md5($_POST['password']) === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
        } else {
            throw new Exception('incorrect password');
        }
    } else {
        throw new Exception('undefined user');
    }

    ResponseJson::sendSuccess('authentication success');
} catch (Throwable $ex) {
    ResponseJson::sendError($ex->getMessage());
}