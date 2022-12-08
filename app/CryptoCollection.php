<?php

namespace App;

use App\Models\Crypto;

class CryptoCollection
{
    public array $cryptos = [];

    public function __construct(array $cryptos = [])
    {
        foreach ($cryptos as $crypto) {
            $this->addCrypto($crypto);
        }
    }

    public function addCrypto(Crypto ...$cryptos)
    {
        $this->cryptos = array_merge($this->cryptos, $cryptos);
    }
}