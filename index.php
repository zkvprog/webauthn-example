<?php

require 'vendor/autoload.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $template = file_get_contents('template/account.html');

    $template = str_replace('{{user_name}}', $_SESSION['user_name'], $template);
} else {
    $template = file_get_contents('template/main.html');
}

echo $template;