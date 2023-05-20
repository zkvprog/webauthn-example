<?php

namespace App\Exception;

use App\System\Interfaces\RenderableInterface;
use App\System\View;

class NotFoundException extends HttpException implements RenderableInterface
{
    public function render()
    {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");

        $view = new View('errors/error', ['title' => 'Error 404. Page not found']);
        $view->render();
    }
}
