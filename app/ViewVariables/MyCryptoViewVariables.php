<?php

namespace App\ViewVariables;

use App\Database;

class MyCryptoViewVariables implements ViewVariables
{
    public function getName(): string
    {
        return 'cryptoCurrencies';
    }

    public function getValue(): array
    {
        //OWNED CRYPTO
        $ownedCrypto = Database::getConnection()->executeQuery(
            "SELECT crypto_name, crypto_count, crypto_price, crypto_solo_price, trade FROM crypto 
                 WHERE id = '{$_SESSION['auth_id']}' AND trade = 'owned'"
        )->fetchAllAssociative();

        //TRANSACTIONS
        $count = Database::getConnection()->executeQuery(
            "SELECT name, count, price, transaction, time
                 FROM transactions WHERE id = '{$_SESSION['auth_id']}'"
        )->fetchAllAssociative();

        return [
            'crypto' => $count,
            'owned' => $ownedCrypto,
        ];
    }

}