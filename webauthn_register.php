<?php

require 'vendor/autoload.php';

use App\DbConnector;
use App\ResponseJson;
use App\WebAuthnAdapter;

try {
    session_start();

    $post = trim(file_get_contents('php://input'));
    if ($post) {
        $post = json_decode($post);
    }

    if (isset($_SESSION['user_id'])) {
        $pdo = DbConnector::getPdoInstance();
        $stmt = $pdo->prepare("SELECT id,name FROM users WHERE id=?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new Exception('undefined user');
        }

        $WebAuthn = new WebAuthnAdapter('Localhost', 'localhost');

        $clientDataJSON = base64_decode($post->clientDataJSON);
        $attestationObject = base64_decode($post->attestationObject);
        $challenge = $_SESSION['webauthn_challenge'];

        $data = $WebAuthn->register($clientDataJSON, $attestationObject, $challenge);

        $stmt = $pdo->prepare("INSERT INTO users_webauthn_credentials (credential_id, user_id, publickey, signature_counter) VALUES(?, ?, ?, ?)");
        $stmt->execute([$data['credentialId'], $_SESSION['user_id'], $data['credentialPublicKey'], $data['signatureCounter']]);

        ResponseJson::sendSuccess('registration passkey success');
    } else {
        throw new Exception('undefined user');
    }
} catch (Throwable $ex) {
    ResponseJson::sendError($ex->getMessage());
}
