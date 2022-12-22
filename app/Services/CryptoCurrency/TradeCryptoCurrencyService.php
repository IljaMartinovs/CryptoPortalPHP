<?php

namespace App\Services\CryptoCurrency;

use App\CryptoValidation;
use App\Models\Collection\CryptoCurrenciesCollection;
use App\Redirect;
use App\Repositories\UserCryptoRepository\MySQLCryptoRepository;

class TradeCryptoCurrencyService
{
    private MySQLCryptoRepository $mySQLCryptoRepository;
    private CryptoValidation $validation;

    public function __construct( MySQLCryptoRepository $mySQLCryptoRepository,
                                 CryptoValidation $validation)
    {

        $this->mySQLCryptoRepository = $mySQLCryptoRepository;
        $this->validation = $validation;
    }

    public function buy(CryptoCurrenciesCollection $cryptoCurrenciesCollection, float $quantity): ?Redirect
    {
        $info = $this->getInfo($cryptoCurrenciesCollection);
        $this->validation->buyCryptoValidate($info[1], $info[0], $quantity);
        if ($this->validation->validationFailed()) {
            return new Redirect('/');
        }
        $this->mySQLCryptoRepository->buy($info[0], $info[1], $quantity);
        return null;
    }

    public function sell(CryptoCurrenciesCollection $cryptoCurrenciesCollection, float $quantity): ?Redirect
    {
        $info = $this->getInfo($cryptoCurrenciesCollection);
        $this->validation->sellCryptoValidate($info[1],$info[0], $quantity);
        if ( $this->validation->validationFailed()) {
            return new Redirect('/');
        }
        $this->mySQLCryptoRepository->sell($info[0], $info[1], $quantity);
        return null;
    }

    public function sellShort(CryptoCurrenciesCollection $cryptoCurrenciesCollection, float $quantity): ?Redirect
    {
        $info = $this->getInfo($cryptoCurrenciesCollection);
        $this->mySQLCryptoRepository->sellShort($info[0], $info[1], $quantity);
        return null;
    }

    public function closeShort(CryptoCurrenciesCollection $cryptoCurrenciesCollection, float $amount): ?Redirect
    {
        $info = $this->getInfo($cryptoCurrenciesCollection);
        $this->validation->closeShort($info[1],$amount);
        if ( $this->validation->validationFailed()) {
            return new Redirect('/orders');
        }
        $this->mySQLCryptoRepository->closeShort($info[0], $info[1],$amount);
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