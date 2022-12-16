<?php

namespace App\ViewVariables;

use App\Database;
use App\Services\CryptoCurrency\ListCryptoCurrencyService;

class MyCryptoViewVariables implements ViewVariables
{
    public function getName(): string
    {
        return 'cryptoCurrencies';
    }

    public function getValue(): array
    {
        return $_SESSION['cryptoCurrencies'] ?? [];
    }

}