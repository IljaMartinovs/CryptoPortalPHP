<?php

namespace App\Repositories;

use App\CryptoCollection;

interface CryptoRepository
{
    public function getCrypto(string $start, string $limit, string $convert = 'USD'): CryptoCollection;
}