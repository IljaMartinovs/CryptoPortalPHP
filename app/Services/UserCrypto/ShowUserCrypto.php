<?php

namespace App\Services\UserCrypto;

use App\Models\Collection\CryptoCurrenciesCollection;
use App\Repositories\CryptoCurrency\CoinMarketCapCryptoCurrenciesRepository;
use App\Repositories\CryptoCurrency\CryptoCurrenciesRepository;

class ShowUserCrypto
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