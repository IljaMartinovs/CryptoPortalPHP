<?php

namespace App\Models;

class CryptoCurrency
{
    private string $symbols;
    private string $name;
    private string $price;
    private string $percentChange1h;
    private string $percentChange24h;
    private string $percentChange7d;
    private ?string $logoURL;

    public function __construct(
        string $symbols,
        string $name,
        string $price,
        string $percentChange1h,
        string $percentChange24h,
        string $percentChange7d,
        ?string $logoURL=null
    )
    {
        $this->symbols = $symbols;
        $this->name = $name;
        $this->price = $price;
        $this->percentChange1h = $percentChange1h;
        $this->percentChange24h = $percentChange24h;
        $this->percentChange7d = $percentChange7d;
        $this->logoURL = $logoURL;
    }

    public function getSymbols(): string
    {
        return $this->symbols;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function getPercentChange1h(): string
    {
        return $this->percentChange1h;
    }

    public function getPercentChange24h(): string
    {
        return $this->percentChange24h;
    }

    public function getPercentChange7d(): string
    {
        return $this->percentChange7d;
    }

    public function getLogoURL(): ?string
    {
        return $this->logoURL;
    }
}
