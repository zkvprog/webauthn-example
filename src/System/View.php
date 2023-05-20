<?php

namespace App\System;

use App\Exception\ApplicationException;
use App\System\Interfaces\RenderableInterface;

class View implements RenderableInterface
{
    public function __construct(private string $template, private array $data)
    {
        return $this;
    }

    public function render(): false|string
    {
        extract($this->data);

        $templateFileName = $this->getIncludeTemplate($this->template);
        if (file_exists($templateFileName)) {
            ob_start();
            include $templateFileName;
            $content = ob_get_contents();
            ob_end_clean();

            return $content;
        } else {
            throw new ApplicationException($this->template . ' шаблон не найден');
        }
    }

    private function getIncludeTemplate($view): string
    {
        $view = str_replace('.', DIRECTORY_SEPARATOR, $view);
        return 'view' . DIRECTORY_SEPARATOR . $view . '.php';
    }
}