<?php

namespace App\Webauthn;

class WebAuthnAdapter
{
    protected $provider;

    const REQUIRE_RESIDENT_KEY = false;
    const REQUIRE_USER_VERIFICATION = false;
    const CROSS_PLATFORM_ATTACHMENT = null;
    const REQUIRE_USER_PRESENT = true;
    const FAIL_ROOT_MISMATCH = false;

    const TIMEOUT = 60;

    const ALLOW_USB = true;
    const ALLOW_NFC = true;
    const ALLOW_BLE = true;
    const ALLOW_INTERNAL = true;

    public function __construct($rpName, $rpId)
    {
        $this->provider = new \lbuchs\WebAuthn\WebAuthn($rpName, $rpId);
    }

    public function getRegisterArgs($userId, $userName, $userDisplayName = '')
    {
        return $this->provider->getCreateArgs(\hex2bin($userId), $userName, $userDisplayName, self::TIMEOUT, self::REQUIRE_RESIDENT_KEY, self::REQUIRE_USER_VERIFICATION, self::CROSS_PLATFORM_ATTACHMENT);
    }

    public function getAuthenticateArgs($credentialIds = null)
    {
        return $this->provider->getGetArgs(null, self::TIMEOUT, self::ALLOW_USB, self::ALLOW_NFC, self::ALLOW_BLE, self::ALLOW_INTERNAL, self::REQUIRE_USER_VERIFICATION);
    }

    public function register($clientDataJSON, $attestationObject, $challenge)
    {
        $data = $this->provider->processCreate($clientDataJSON, $attestationObject, $challenge, self::REQUIRE_USER_VERIFICATION, self::REQUIRE_USER_PRESENT, self::FAIL_ROOT_MISMATCH);

        return [
            'credentialId' => base64_encode($data->credentialId),
            'credentialPublicKey' => $data->credentialPublicKey,
            'signatureCounter' => $data->signatureCounter
        ];
    }

    public function getChallenge()
    {
        return $this->provider->getChallenge();
    }


}