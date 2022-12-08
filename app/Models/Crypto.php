<?php

namespace App\Models;

class Crypto
{
    private string $name;
    private string $symbol;
    private string $price;
    private string $profit;

    public function __construct(string $name, string $symbol, float $price, float $profit)
    {
        $this->name = $name;
        $this->symbol = $symbol;
        $this->price = $price;
        $this->profit = $profit;
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

    public function getProfit(): string
    {
        return $this->profit;
    }
}