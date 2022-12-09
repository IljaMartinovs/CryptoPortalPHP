<?php

namespace App\Models\Collections;

use App\Models\Crypto;

class CryptoCollection
{
    public array $cryptos = [];

    public function addCrypto(Crypto ...$cryptos)
    {
        $this->cryptos = array_merge($this->cryptos, $cryptos);
    }
}