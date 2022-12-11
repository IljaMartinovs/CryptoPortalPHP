<?php


namespace App\Services\CryptoCurrency;

use App\Models\Collection\CryptoCurrenciesCollection;
use App\Repositories\CryptoCurrency\CoinMarketCapCryptoCurrenciesRepository;
use App\Repositories\CryptoCurrency\CryptoCurrenciesRepository;

class ListCryptoCurrencyService
{
    private CryptoCurrenciesRepository $cryptoCurrenciesCollection;

    public function __construct()
    {
        $this->cryptoCurrenciesCollection = new CoinMarketCapCryptoCurrenciesRepository();
    }

    public function execute(array $symbols, ?string $single): CryptoCurrenciesCollection
    {
        return $this->cryptoCurrenciesCollection->findAllBySymbols($symbols, $single);
    }

}