<?php

require 'vendor/autoload.php';

use App\DbConnector;
use App\ResponseJson;
use App\WebAuthnAdapter;

try {
    session_start();

    $WebAuthn = new WebAuthnAdapter('Localhost', 'localhost');

    $cmd = filter_input(INPUT_GET, 'cmd');

    $pdo = DbConnector::getPdoInstance();

    if ($cmd == 'getRegisterArgs') {

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
            throw new Exception('undefined user');
        }
        #endregion

        $args = $WebAuthn->getRegisterArgs($userId, $userName, $userDisplayName);
    } elseif ($cmd == 'getAuthenticateArgs')  {
        $args = $WebAuthn->getAuthenticateArgs(null);
    } else {
        throw new Exception('unknown cmd');
    }

    ResponseJson::sendSuccess('got webauthn args success', json_encode($args));
    $_SESSION['webauthn_challenge'] = $WebAuthn->getChallenge();

} catch (Throwable $ex) {
    ResponseJson::sendError($ex->getMessage());
}