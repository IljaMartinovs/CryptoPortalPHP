<?php

namespace App\Repositories\CryptoCurrency;

use App\Models\Collection\CryptoCurrenciesCollection;

interface CryptoCurrenciesRepository
{
    public function findAllBySymbols(array $symbols, ?string $single): CryptoCurrenciesCollection;
}
