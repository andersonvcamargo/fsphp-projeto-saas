<?php

namespace Source\Core;

use League\Plates\Engine;

class View
{
    /** @var Engine */
    private $engine;

    /** @var string */
    private $path;

    /**
     * View constructor
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->engine = new Engine($this->path);
    }

    /**
     * Renderiza o template
     */
   public function render(string $templateName, array $data = []): string
    {
        return $this->engine->render($templateName, $data);
    }

    /**
     * Retorna caminho do tema
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Retorna caminho para assets do tema
     */
    public function asset(string $path): string
    {
        return url("themes/" . CONF_VIEW_THEME . "/assets/{$path}");
    }
}