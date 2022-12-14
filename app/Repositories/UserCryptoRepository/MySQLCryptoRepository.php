<?php

namespace App\Repositories\UserCryptoRepository;

use App\Database;
use App\Services\CryptoCurrency\TradeCryptoCurrencyService;
use App\Services\EditService;

class MySQLCryptoRepository implements UserCryptoRepository
{
    public function buy(float $price, string $symbol, float $count): void
    {
//        Database::getConnection()->executeQuery("DELETE FROM crypto WHERE id = 18")->fetchAllAssociative();
//        die;
        $soloPrice = $price;
        $price *= $count;
        $trade = 'purchased';
        $owned = 'owned';

        //ADD TRANSACTION
        Database::getConnection()->executeQuery(
            "INSERT INTO transactions (id, name, count, price, transaction)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$price','$trade')"
        )->fetchAllAssociative();


        //GET QUERY AND CHECK IF OWNED WITH THIS CRYPTO EXIST
        $query = Database::getConnection()->executeQuery("SELECT * FROM crypto
         WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = '$owned'")->fetchAllAssociative();

        //IF NOT EXIST ADD TO DATABASE
        if (count($query) == 0) {
            Database::getConnection()->executeQuery(
                "INSERT INTO crypto (id, crypto_name, crypto_count,crypto_solo_price, crypto_price, trade)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$soloPrice','$price','$owned')"
            )->fetchAllAssociative();

        } else if (count($query) == 1) {

            //SELECT PRICE FROM TRANSACTION WITH SAME SYMBOL
            $currentCryptoCount = Database::getConnection()->executeQuery(
                "SELECT price FROM transactions
                     WHERE id = '{$_SESSION['auth_id']}' AND name = '$symbol'  AND transaction = '$trade'"
            )->fetchAllAssociative();

            $currentPriceForCrypto = (float)($currentCryptoCount[1]["price"]);

            //UPDATE CRYPTO DATABASE
            Database::getConnection()->executeQuery(
                "UPDATE crypto SET crypto_count = crypto_count + '$count', crypto_price = crypto_price + '$currentPriceForCrypto'
              WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = '$owned'"
            )->fetchAllAssociative();
        }
        //SUBTRACT MONEY FROM USER
        (new EditService())->changeUserMoney(-$price);
    }

    public function sell(float $price, string $symbol, float $count): void
    {
        $cryptoAmount = Database::getConnection()->executeQuery("SELECT crypto_count FROM crypto 
                    WHERE id= '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'owned'")->fetchAssociative();
        $newMoney = $price * $count;
        $newAmount = $cryptoAmount['crypto_count'] - $count;
        $trade = 'sold';

        if ($newAmount > 0) {
            //UPDATE CRYPTO OWNED PRICE
            Database::getConnection()->executeQuery(
                "UPDATE crypto SET crypto_count = crypto_count - '$count', crypto_price = crypto_price - '$newMoney'
              WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'owned'"
            )->fetchAllAssociative();

        } else if ($newAmount == 0 || $newAmount == null) {
            Database::getConnection()->executeQuery(
                "DELETE FROM crypto WHERE crypto_name = '$symbol'"
            )->fetchAllAssociative();
        }

        //INSERT TRANSACTION INTO TRANSACTIONS
        Database::getConnection()->executeQuery(
            "INSERT INTO transactions (id, name, count, price, transaction)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$newMoney','$trade')"
        )->fetchAllAssociative();

        (new EditService())->changeUserMoney($newMoney);
    }

}