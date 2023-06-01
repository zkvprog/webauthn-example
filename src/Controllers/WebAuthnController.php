<?php

namespace App\Controllers;

use App\Exception\ApplicationException;
use App\Repositories\UserRepository;
use App\System\DIContainerFacade;
use App\System\ResponseJson;
use App\Services\WebAuthnAdapter;

class WebAuthnController
{
    public function authenticate(): ResponseJson
    {
        try {
            $post = trim(file_get_contents('php://input'));
            if ($post) {
                $post = json_decode($post);
            }

            $webAuthn = new WebAuthnAdapter(DIContainerFacade::get('webauthn'));
            //new WebAuthn($_ENV['APP_NAME'], $_ENV['DOMAIN_NAME']);

            $clientDataJSON = base64_decode($post->clientDataJSON);
            $authenticatorData = base64_decode($post->authenticatorData);
            $signature = base64_decode($post->signature);
            $userHandle = base64_decode($post->userHandle);
            $challenge = $_SESSION['webauthn_challenge'];
            $credentialPublicKey = null;

            $db = DIContainerFacade::get('db');
            $stmt = $db->prepare("SELECT publickey, user_id FROM users_webauthn_credentials WHERE credential_id=?");
            $stmt->execute([$post->id]);
            $credentialInfo = $stmt->fetch();

            if ($credentialInfo === null) {
                throw new ApplicationException('Public Key for credential ID not found!');
            }

            $result = $webAuthn->authenticate($clientDataJSON, $authenticatorData, $signature, $credentialInfo['publickey'], $challenge);
            if ($result) {
                $_SESSION['user_id'] = $credentialInfo['user_id'];
                $_SESSION['user_name'] = $credentialInfo['user_id'];
            } else {
                throw new ApplicationException('Unsuccess authenticate');
            }

            return new ResponseJson(true, 'got passkey success');
        } catch (ApplicationException $ex) {
            return new ResponseJson(false, $ex->getMessage());
        }
    }

    public function getArgs(): ResponseJson
    {
        try {
            $WebAuthn = new WebAuthnAdapter(DIContainerFacade::get('webauthn'));
            $cmd = filter_input(INPUT_GET, 'cmd');
            $db = DIContainerFacade::get('db');

            if ($cmd === 'getRegisterArgs') {
                if ($_SESSION['user_id']) {
                    $userRepository = new UserRepository($db);
                    $user = $userRepository->read(
                        condition: 'id=:id',
                        params: ['id' => $_SESSION['user_id']],
                        readOne: true
                    );

                    $userId = $user['webauthn_id'];
                    $userName = $user['name'];
                    $userDisplayName = $user['name'];

                    $stmt = $db->prepare("SELECT credential_id  FROM users_webauthn_credentials WHERE user_id=?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $users_webauthn_credentials = $stmt->fetch();
                } else {
                    throw new ApplicationException('undefined user');
                }

                $args = $WebAuthn->getRegisterArgs($userId, $userName, $userDisplayName);
            } elseif ($cmd === 'getAuthenticateArgs')  {
                $args = $WebAuthn->getAuthenticateArgs(null);
            } else {
                throw new ApplicationException('unknown cmd');
            }

            $_SESSION['webauthn_challenge'] = $WebAuthn->getChallenge();

            return new ResponseJson(true, 'got passkey success', $args);
        } catch (ApplicationException $ex) {
            return new ResponseJson(false, $ex->getMessage());
        }
    }

    public function register(): ResponseJson
    {
        try {
            $post = trim(file_get_contents('php://input'));
            if ($post) {
                $post = json_decode($post);
            }

            if (isset($_SESSION['user_id'])) {
                $db = DIContainerFacade::get('db');
                $userRepository = new UserRepository($db);
                $user = $userRepository->read(
                    condition: 'id=:id',
                    params: ['id' => $_SESSION['user_id']],
                    readOne: true
                );

                if (empty($user)) {
                    throw new ApplicationException('undefined user');
                }

                $webAuthn = new WebAuthnAdapter(DIContainerFacade::get('webauthn'));

                $clientDataJSON = base64_decode($post->clientDataJSON);
                $attestationObject = base64_decode($post->attestationObject);
                $challenge = $_SESSION['webauthn_challenge'];

                $data = $webAuthn->register($clientDataJSON, $attestationObject, $challenge);

                $stmt = $db->prepare("INSERT INTO users_webauthn_credentials (credential_id, user_id, publickey, signature_counter) VALUES(?, ?, ?, ?)");
                $stmt->execute([$data['credentialId'], $_SESSION['user_id'], $data['credentialPublicKey'], $data['signatureCounter']]);

                return new ResponseJson(true, 'registration passkey success');
            } else {
                throw new ApplicationException('undefined user');
            }
        } catch (ApplicationException $ex) {
            return new ResponseJson(false, $ex->getMessage());
        }
    }
}