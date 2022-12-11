<?php

namespace App;

class View
{
    private string $path;
    private array $data;

    public function __construct(string $path, array $data)
    {
        $this->path = $path;
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public static function render(string $path, array $data): View
    {
        return new View($path, $data);
    }
}