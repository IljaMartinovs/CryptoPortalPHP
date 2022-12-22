<?php

namespace App\Services\CryptoCurrency;

use App\Models\Collection\CryptoCurrenciesCollection;
use App\Repositories\CryptoCurrency\CryptoCurrenciesRepository;

class ListCryptoCurrencyService
{
    private CryptoCurrenciesRepository $cryptoCurrenciesCollection;

    public function __construct(CryptoCurrenciesRepository $cryptoCurrenciesCollection)
    {
        $this->cryptoCurrenciesCollection = $cryptoCurrenciesCollection;
    }

    public function execute(array $symbols, ?string $single): CryptoCurrenciesCollection
    {
        return $this->cryptoCurrenciesCollection->findAllBySymbols($symbols, $single);
    }
}