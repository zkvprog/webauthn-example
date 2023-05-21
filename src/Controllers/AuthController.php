<?php

namespace App\Controllers;

use App\Db\DbConnector;
use App\System\ResponseJson;
use Throwable;

class AuthController
{
    public function signup(): ResponseJson
    {
        try {
            session_start();

            $pdo = DbConnector::getPdoInstance();
            $stmt = $pdo->prepare("INSERT INTO users (name, webauthn_id, password) VALUES(?, ?, ?)");
            $stmt->execute([$_POST['username'], bin2hex(random_bytes(16)), md5($_POST['password'])]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $_POST['username'];

            return new ResponseJson(true, 'registration success');
        } catch (Throwable $ex) {
            return new ResponseJson(false, $ex->getMessage());
        }
    }

    public function login()
    {
        try {
            $pdo = DbConnector::getPdoInstance();
            $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE name=?");
            $stmt->execute([$_POST['username']]);
            $user = $stmt->fetch();

            if (isset($user)) {
                if (md5($_POST['password']) === $user['password']) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                } else {
                    throw new \Exception('incorrect password');
                }
            } else {
                throw new \Exception('undefined user');
            }

            return new ResponseJson(true, 'authentication success');
        } catch (Throwable $ex) {
            return new ResponseJson(false, $ex->getMessage());
        }
    }

    public function logout()
    {
        try {
            session_start();
            session_destroy();

            return new ResponseJson(true, 'logout success');
        } catch (Throwable $ex) {
            return new ResponseJson(false, $ex->getMessage());
        }
    }
}