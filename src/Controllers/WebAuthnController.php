<?php

namespace App\Controllers;

use App\Db\PdoConnector;
use App\Exception\ApplicationException;
use App\System\ResponseJson;
use App\Webauthn\WebAuthnAdapter;
use lbuchs\WebAuthn\WebAuthn;
use Throwable;

class WebAuthnController
{
    public function authenticate(): ResponseJson
    {
        try {
            $post = trim(file_get_contents('php://input'));
            if ($post) {
                $post = json_decode($post);
            }

            $WebAuthn = new WebAuthn($_ENV['APP_NAME'], $_ENV['DOMAIN_NAME']);

            $clientDataJSON = base64_decode($post->clientDataJSON);
            $authenticatorData = base64_decode($post->authenticatorData);
            $signature = base64_decode($post->signature);
            $userHandle = base64_decode($post->userHandle);
            $challenge = $_SESSION['webauthn_challenge'];
            $credentialPublicKey = null;

            $pdo = PdoConnector::getPdoInstance();

            $stmt = $pdo->prepare("SELECT publickey, user_id FROM users_webauthn_credentials WHERE credential_id=?");
            $stmt->execute([$post->id]);
            $credentialInfo = $stmt->fetch();

            if ($credentialInfo === null) {
                throw new ApplicationException('Public Key for credential ID not found!');
            }

            $result = $WebAuthn->processGet($clientDataJSON, $authenticatorData, $signature, $credentialInfo['publickey'], $challenge, null, 'false');
            if ($result) {
                $_SESSION['user_id'] = $credentialInfo['user_id'];
                $_SESSION['user_name'] = $credentialInfo['user_id'];
            } else {
                throw new ApplicationException('Unsuccess authenticate');
            }

            return new ResponseJson(true, 'got passkey success');
        } catch (Throwable $ex) {
            return new ResponseJson(false, $ex->getMessage());
        }
    }

    public function getArgs(): ResponseJson
    {
        try {
            $WebAuthn = new WebAuthnAdapter($_ENV['APP_NAME'], $_ENV['DOMAIN_NAME']);
            $cmd = filter_input(INPUT_GET, 'cmd');
            $pdo = PdoConnector::getPdoInstance();

            if ($cmd === 'getRegisterArgs') {
                #region init user and user_credentials
                if ($_SESSION['user_id']) {
                    $stmt = $pdo->prepare("SELECT id, name, webauthn_id FROM users WHERE id=?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch();

                    $userId = $user['webauthn_id'];
                    $userName = $user['name'];
                    $userDisplayName = $user['name'];

                    $stmt = $pdo->prepare("SELECT credential_id  FROM users_webauthn_credentials WHERE user_id=?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $users_webauthn_credentials = $stmt->fetch();
                } else {
                    throw new ApplicationException('undefined user');
                }
                #endregion

                $args = $WebAuthn->getRegisterArgs($userId, $userName, $userDisplayName);
            } elseif ($cmd === 'getAuthenticateArgs')  {
                $args = $WebAuthn->getAuthenticateArgs(null);
            } else {
                throw new ApplicationException('unknown cmd');
            }

            $_SESSION['webauthn_challenge'] = $WebAuthn->getChallenge();

            return new ResponseJson(true, 'got passkey success', $args);
        } catch (Throwable $ex) {
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
                $pdo = PdoConnector::getPdoInstance();
                $stmt = $pdo->prepare("SELECT id,name FROM users WHERE id=?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch();

                if (!$user) {
                    throw new ApplicationException('undefined user');
                }

                $WebAuthn = new WebAuthnAdapter($_ENV['APP_NAME'], $_ENV['DOMAIN_NAME']);

                $clientDataJSON = base64_decode($post->clientDataJSON);
                $attestationObject = base64_decode($post->attestationObject);
                $challenge = $_SESSION['webauthn_challenge'];

                $data = $WebAuthn->register($clientDataJSON, $attestationObject, $challenge);

                $stmt = $pdo->prepare("INSERT INTO users_webauthn_credentials (credential_id, user_id, publickey, signature_counter) VALUES(?, ?, ?, ?)");
                $stmt->execute([$data['credentialId'], $_SESSION['user_id'], $data['credentialPublicKey'], $data['signatureCounter']]);

                return new ResponseJson(true, 'registration passkey success');
            } else {
                throw new ApplicationException('undefined user');
            }
        } catch (Throwable $ex) {
            return new ResponseJson(false, $ex->getMessage());
        }
    }
}