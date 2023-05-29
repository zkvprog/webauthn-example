<?php

namespace App\Controllers;

use App\Db\DbConnector;
use App\Exception\ApplicationException;
use App\System\ResponseJson;
use Throwable;

class AuthController
{
    public function signup(): ResponseJson
    {
        try {
            $pdo = DbConnector::getPdoInstance();

            $stmt = $pdo->prepare("SELECT id FROM users WHERE name=?");
            $stmt->execute([$_POST['username']]);
            $existingUser = $stmt->fetch();
            if (isset($existingUser)) {
                throw new ApplicationException('user with this name already exists');
            }

            $stmt = $pdo->prepare("INSERT INTO users (name, webauthn_id, password) VALUES(?, ?, ?)");
            $stmt->execute([$_POST['username'], bin2hex(random_bytes(16)), password_hash($_POST['password'], PASSWORD_DEFAULT )]);

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
                if (password_verify($_POST['password'], $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                } else {
                    throw new ApplicationException('incorrect password');
                }
            } else {
                throw new ApplicationException('undefined user');
            }

            return new ResponseJson(true, 'authentication success');
        } catch (Throwable $ex) {
            return new ResponseJson(false, $ex->getMessage());
        }
    }

    public function logout()
    {
        try {
            session_destroy();

            return new ResponseJson(true, 'logout success');
        } catch (Throwable $ex) {
            return new ResponseJson(false, $ex->getMessage());
        }
    }
}