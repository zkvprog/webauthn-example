<?php

namespace App\Controllers;

use App\Exception\ApplicationException;
use App\Repositories\UserRepository;
use App\System\DIContainerFacade;
use App\System\ResponseJson;
use Throwable;

class AuthController
{
    public function signup(): ResponseJson
    {
        try {
            $userRepository = new UserRepository(DIContainerFacade::get('db'));
            $existingUser = $userRepository->read('name=:name', ['name' => $_POST['username']]);

            if (!empty($existingUser)) {
                throw new ApplicationException('user with this name already exists');
            }

            $_SESSION['user_id'] = $userRepository->create([
                'name' => $_POST['username'],
                'webauthn_id' => bin2hex(random_bytes(16)),
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT )
            ]);
            $_SESSION['user_name'] = $_POST['username'];

            return new ResponseJson(true, 'registration success');
        } catch (Throwable $ex) {
            return new ResponseJson(false, $ex->getMessage());
        }
    }

    public function login(): ResponseJson
    {
        try {
            $userRepository = new UserRepository(DIContainerFacade::get('db'));
            $user = $userRepository->read(
                condition: 'name=:name',
                params: ['name' => $_POST['username']],
                readOne: true
            );

            if (!empty($user)) {
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

    public function logout(): ResponseJson
    {
        try {
            session_destroy();

            return new ResponseJson(true, 'logout success');
        } catch (Throwable $ex) {
            return new ResponseJson(false, $ex->getMessage());
        }
    }
}