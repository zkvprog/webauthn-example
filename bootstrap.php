<?php

use App\Db\PdoConnector;
use App\System\DIContainer;
use App\System\DIContainerFacade;

require 'vendor/autoload.php';

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$diContainer = new DIContainer([
    'db' => function () {
        return PdoConnector::getInstance();
    },
    'webauthn' => function () {
        return new \lbuchs\WebAuthn\WebAuthn($_ENV['APP_NAME'], $_ENV['DOMAIN_NAME']);
    }
]);

DIContainerFacade::setContainer($diContainer);