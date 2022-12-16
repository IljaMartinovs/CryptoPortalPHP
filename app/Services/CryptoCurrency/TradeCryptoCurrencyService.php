<?php

namespace App\Services\CryptoCurrency;

use App\Models\Collection\CryptoCurrenciesCollection;
use App\Redirect;
use App\Repositories\UserCryptoRepository;
use App\Validation;

class TradeCryptoCurrencyService
{
    private UserCryptoRepository\MySQLCryptoRepository $mySQLCryptoRepository;

    public function __construct()
    {
        $this->mySQLCryptoRepository = new UserCryptoRepository\MySQLCryptoRepository();
    }

    public function buy(CryptoCurrenciesCollection $cryptoCurrenciesCollection, float $quantity): ?Redirect
    {
        $info = $this->getInfo($cryptoCurrenciesCollection);
        $validation = new Validation();

        $validation->buyCryptoValidate($info[1], $info[0], $quantity);
        if ($validation->validationFailed()) {
            return new Redirect('/');
        }
        $this->mySQLCryptoRepository->buy($info[0], $info[1], $quantity);
        return null;
    }

    public function sell(CryptoCurrenciesCollection $cryptoCurrenciesCollection, float $quantity): ?Redirect
    {
        //NEED VALIDATION
        $info = $this->getInfo($cryptoCurrenciesCollection);
        $validation = new Validation();
        $validation->sellCryptoValidate($info[1], $quantity);
        if ($validation->validationFailed()) {
            return new Redirect('/');
        }
        $this->mySQLCryptoRepository->sell($info[0], $info[1], $quantity);
        return null;
    }

    private function getInfo(CryptoCurrenciesCollection $cryptoCurrenciesCollection): array
    {
        foreach ($cryptoCurrenciesCollection->all() as $crypto) {
            (int)$price = $crypto->getPrice();
            $symbol = $crypto->getSymbols();
        }
        return [$price, $symbol];
    }
}