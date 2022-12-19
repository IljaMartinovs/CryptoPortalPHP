<?php

namespace App\Services\UserShorts;

use App\Models\Collection\CryptoCurrenciesCollection;
use App\Repositories\CryptoCurrency\CoinMarketCapCryptoCurrenciesRepository;
use App\Repositories\CryptoCurrency\CryptoCurrenciesRepository;

class ShowUserShorts
{
    private CryptoCurrenciesRepository $cryptoCurrenciesRepository;
    public function __construct()
    {
        $this->cryptoCurrenciesRepository = new CoinMarketCapCryptoCurrenciesRepository();
    }

    public function execute(array $userCrypto): CryptoCurrenciesCollection
    {
        return $this->cryptoCurrenciesRepository->findAllBySymbols($userCrypto,$single=null);
    }
}