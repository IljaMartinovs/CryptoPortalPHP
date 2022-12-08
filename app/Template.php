<?php

namespace App;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Template
{
    private string $template;
    private array $context;

    public function __construct(string $template, array $context = [])
    {
        $this->template = $template;
        $this->context = $context;
    }

    public function render(): void
    {
        $loader = new FilesystemLoader('public/views');
        $twig = new Environment($loader);
        $twig->addGlobal('session', $_SESSION);
        echo $twig->render($this->template, $this->context);
    }
}