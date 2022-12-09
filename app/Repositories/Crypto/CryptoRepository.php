<?php

namespace App\Repositories\Crypto;

use App\Models\Collections\CryptoCollection;

interface CryptoRepository
{
    public function getCrypto(): CryptoCollection;
}