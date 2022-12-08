<?php

namespace App\Repositories;

use App\CryptoCollection;

interface CryptoRepository
{
    public function getCrypto(): CryptoCollection;
}