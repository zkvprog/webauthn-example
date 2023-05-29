<?php

use App\Controllers\{HomeController, AuthController, WebAuthnController};
use App\System\{Router, Request, System};

require_once __DIR__ . '/bootstrap.php';

$router = new Router();

$router->get('', [HomeController::class, 'index']);
$router->post('signup', [AuthController::class, 'signup']);
$router->post('login', [AuthController::class, 'login']);
$router->get('logout', [AuthController::class, 'logout']);
$router->get('webauthn_authenticate', [WebAuthnController::class, 'authenticate']);
$router->get('webauthn_get_args', [WebAuthnController::class, 'getArgs']);
$router->get('webauthn_register', [WebAuthnController::class, 'register']);

$app = new System($router);
$app->run(Request::getPath(), Request::getMethod());