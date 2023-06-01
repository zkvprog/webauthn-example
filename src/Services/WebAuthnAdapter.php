<?php

namespace App\Services;

use lbuchs\WebAuthn\Binary\ByteBuffer;

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

    public function __construct(object $providerWebAuthn)
    {
        $this->provider = $providerWebAuthn;
    }

    public function getRegisterArgs(string|int $userId, string $userName, string $userDisplayName = ''): \stdClass
    {
        return $this->provider->getCreateArgs(\hex2bin($userId), $userName, $userDisplayName, self::TIMEOUT, self::REQUIRE_RESIDENT_KEY, self::REQUIRE_USER_VERIFICATION, self::CROSS_PLATFORM_ATTACHMENT);
    }

    public function getAuthenticateArgs(array $credentialIds = []): \stdClass
    {
        return $this->provider->getGetArgs($credentialIds, self::TIMEOUT, self::ALLOW_USB, self::ALLOW_NFC, self::ALLOW_BLE, self::ALLOW_INTERNAL, self::REQUIRE_USER_VERIFICATION);
    }

    public function authenticate(string $clientDataJSON, string $authenticatorData, string $signature, string $credentialPublicKey, string|ByteBuffer $challenge, ?int $prevSignatureCnt = null): bool
    {
        return $this->provider->processGet($clientDataJSON, $authenticatorData, $signature, $credentialPublicKey, $challenge, $prevSignatureCnt, self::REQUIRE_USER_VERIFICATION, self::REQUIRE_USER_PRESENT);
    }

    public function register(string $clientDataJSON, string $attestationObject, string|ByteBuffer $challenge): array
    {
        $data = $this->provider->processCreate($clientDataJSON, $attestationObject, $challenge, self::REQUIRE_USER_VERIFICATION, self::REQUIRE_USER_PRESENT, self::FAIL_ROOT_MISMATCH);

        return [
            'credentialId' => base64_encode($data->credentialId),
            'credentialPublicKey' => $data->credentialPublicKey,
            'signatureCounter' => $data->signatureCounter
        ];
    }

    public function getChallenge(): ByteBuffer
    {
        return $this->provider->getChallenge();
    }


}