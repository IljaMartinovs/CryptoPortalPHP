<?php

namespace App\Services;

use App\Database;
use App\Models\Collection\CryptoCurrenciesCollection;
use App\Validation;

class EditService
{
    public function changeUserMoney(int $money): void
    {
        $validation = new Validation();
        $validation->changeMoneyValidate($money);
    }

    public function buyCrypto(CryptoCurrenciesCollection $cryptoCurrenciesCollection, int $count): void
    {
        $validation = new Validation();
        $validation->buyCryptoValidate($cryptoCurrenciesCollection, $count);
    }

    public function sellCrypto(CryptoCurrenciesCollection $cryptoCurrenciesCollection, int $count): void
    {
        $validation = new Validation();
        $validation->sellCryptoValidate($cryptoCurrenciesCollection, $count);
    }
}