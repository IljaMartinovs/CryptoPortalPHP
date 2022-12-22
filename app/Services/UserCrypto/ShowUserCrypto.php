<?php

namespace App\Services\UserCrypto;

use App\Models\Collection\CryptoCurrenciesCollection;
use App\Repositories\CryptoCurrency\CryptoCurrenciesRepository;

class ShowUserCrypto
{
    private CryptoCurrenciesRepository $cryptoCurrenciesRepository;
    public function __construct(CryptoCurrenciesRepository $cryptoCurrenciesRepository)
    {
        $this->cryptoCurrenciesRepository = $cryptoCurrenciesRepository;
    }

    public function execute(array $userCrypto,?string $single): CryptoCurrenciesCollection
    {
        return $this->cryptoCurrenciesRepository->findAllBySymbols($userCrypto,$single);
    }

}