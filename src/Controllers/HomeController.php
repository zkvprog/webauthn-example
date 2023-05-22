<?php

namespace App\Controllers;

use App\System\View;

class HomeController
{
    public function index(): View
    {
        if (isset($_SESSION['user_id'])) {
            $template = (new View('account', ['user_name' => $_SESSION['user_name']]));
        } else {
            $template = (new View('main', []));
        }

        return $template;
    }
}