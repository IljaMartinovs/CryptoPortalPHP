<?php

namespace App\Models;

class Crypto
{
    private string $name;
    private string $symbol;
    private string $price;
    private string $change;

    public function __construct(string $name, string $symbol, float $price, float $change)
    {
        $this->name = $name;
        $this->symbol = $symbol;
        $this->price = $price;
        $this->change = $change;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function getChange(): string
    {
        return $this->change;
    }
}