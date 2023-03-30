<?php

require 'vendor/autoload.php';

use App\DbConnector;
use App\ResponseJson;

try {
    session_start();

    $post = trim(file_get_contents('php://input'));
    if ($post) {
        $post = json_decode($post);
    }

    $rpId = 'localhost';
    $WebAuthn = new lbuchs\WebAuthn\WebAuthn('localhost', $rpId);

    $clientDataJSON = base64_decode($post->clientDataJSON);
    $authenticatorData = base64_decode($post->authenticatorData);
    $signature = base64_decode($post->signature);
    $userHandle = base64_decode($post->userHandle);
    $challenge = $_SESSION['webauthn_challenge'];
    $credentialPublicKey = null;

    $pdo = DbConnector::getPdoInstance();

    $stmt = $pdo->prepare("SELECT publickey, user_id FROM users_webauthn_credentials WHERE credential_id=?");
    $stmt->execute([$post->id]);
    $credentialInfo = $stmt->fetch();

    if ($credentialInfo === null) {
        throw new Exception('Public Key for credential ID not found!');
    }

    $result = $WebAuthn->processGet($clientDataJSON, $authenticatorData, $signature, $credentialInfo['publickey'], $challenge, null, 'false');
    if ($result) {
        $_SESSION['user_id'] = $credentialInfo['user_id'];
        $_SESSION['user_name'] = $credentialInfo['user_id'];
    } else {
        throw new Exception('Unsuccess authenticate');
    }

    ResponseJson::sendSuccess('got passkey success');
} catch (Throwable $ex) {
    ResponseJson::sendError($ex->getMessage());
}