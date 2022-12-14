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
            "SELECT crypto_name, crypto_count, crypto_price FROM crypto 
                 WHERE id = '{$_SESSION['auth_id']}'"
        )->fetchAllAssociative();

        //TRANSACTIONS
        $transactions = Database::getConnection()->executeQuery(
            "SELECT name, count, price, transaction, time
                 FROM transactions WHERE id = '{$_SESSION['auth_id']}'"
        )->fetchAllAssociative();

        //CURRENT CRYPTO PRICE
        //FORMULA (current price - my price) / my price * 100
        //(0.126041 - 0.106041) / 0.106041 * 100 = 19.37%

        $profitLoss = Database::getConnection()->executeQuery(
            "SELECT name, count, price, transaction, time
                 FROM transactions WHERE id = '{$_SESSION['auth_id']}'"
        )->fetchAllAssociative();

        return [
            'crypto' => $transactions,
            'owned' => $ownedCrypto,
            'profit' => $profitLoss
        ];
    }

}