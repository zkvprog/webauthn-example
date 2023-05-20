<?php
namespace App\System;

use App\Exception\ApplicationException;
use App\System\Interfaces\RenderableInterface;

class System
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function run(string $url, string $method)
    {
        try {
            $out = $this->router->fetch($url, $method);

            if ($out instanceof RenderableInterface) {
                $out->render();
            } else {
                echo $out;
            }
        } catch (ApplicationException $e) {
            $this->renderException($e);
        }
    }

    private function renderException(ApplicationException $e)
    {
        if ($e instanceof RenderableInterface) {
            $e->render();
        } else {
            http_response_code(empty($e->getCode()) ? 500 : $e->getCode());

            $view = new View('errors/error', ['title' => $e->getMessage()]);
            $view->render();
        }
    }
}
