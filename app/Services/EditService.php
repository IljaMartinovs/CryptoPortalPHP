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
        $response = $validation->changeMoneyValidate($money);
        Database::getConnection()->executeQuery("UPDATE users  SET money = '{$response}' WHERE id= '{$_SESSION['auth_id']}'");
    }

    public function buyCrypto(CryptoCurrenciesCollection $cryptoCurrenciesCollection, float $count): void
    {
        $validation = new Validation();
        $response = $validation->buyCryptoValidate($cryptoCurrenciesCollection, $count);
        $symbol = $response[0];
        $soloPrice = (float)$response[1];
        $price = (float)$response[2];
        $price = round($price, 3);
        $trade = 'purchased';
        $owned = 'owned';
        $query = Database::getConnection()->executeQuery("SELECT * FROM crypto
         WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = '$owned'")->fetchAllAssociative();

        //INSERT into database transaction for cryptocurrency
        Database::getConnection()->executeQuery(
            "INSERT INTO crypto (id, crypto_name, crypto_count,crypto_solo_price, crypto_price, trade)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$soloPrice', CAST($price AS FLOAT),'$trade')"
        )->fetchAllAssociative();

        //IF this cryptocurrency exist then do not put trade = owned but just owned + $count + $price
        if (count($query) == 0) {

            Database::getConnection()->executeQuery(
                "INSERT INTO crypto (id, crypto_name, crypto_count,crypto_solo_price, crypto_price, trade)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$soloPrice','$price','$owned')"
            )->fetchAllAssociative();
        } else if (count($query) == 1) {
            $currentCryptoCount = Database::getConnection()->executeQuery(
                "SELECT crypto_price FROM crypto
                     WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol'  AND trade = '$trade'"
            )->fetchAllAssociative();
            $currentPriceForCrypto = (float)($currentCryptoCount[1]["crypto_price"]);
            Database::getConnection()->executeQuery(
                "UPDATE crypto SET crypto_count = crypto_count + '$count', crypto_price = crypto_price + '$currentPriceForCrypto'
              WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'owned'"
            )->fetchAllAssociative();
        }
    }

    public function sellCrypto(CryptoCurrenciesCollection $cryptoCurrenciesCollection, float $count): void
    {
//        DELETE ALL Database::getConnection()->executeQuery("DELETE FROM crypto WHERE id = 24")->fetchAllAssociative();

        $validation = new Validation();
        $response = $validation->sellCryptoValidate($cryptoCurrenciesCollection, $count);
        $newAmount = $response[0];
        $newMoney = $response[1];
        $symbol = $response[2];
        $price = (float)$response[3];
        $trade = 'sold';
        // IF WANT TO SELL NOT ALL CRYPTOCURRENCY
        if ($newAmount > 0) {
            Database::getConnection()->executeQuery(
                "INSERT INTO crypto (id, crypto_name, crypto_count, crypto_solo_price, crypto_price, trade)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$price','$newMoney','$trade')"
            )->fetchAllAssociative();

            Database::getConnection()->executeQuery(
                "UPDATE crypto SET crypto_count = crypto_count - '$count', crypto_price = crypto_price - '$newMoney'
              WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'owned'"
            )->fetchAllAssociative();
            (new EditService())->changeUserMoney($newMoney);

            // IF WANT TO SELL  ALL CRYPTOCURRENCY
        } else if ($newAmount == 0 || $newAmount == null) {
            Database::getConnection()->executeQuery(
                "INSERT INTO crypto (id, crypto_name, crypto_count, crypto_solo_price, crypto_price,  trade)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$price','$newMoney','$trade')"
            )->fetchAllAssociative();

            //       Database::getConnection()->executeQuery("DELETE FROM crypto WHERE id = 18")->fetchAllAssociative();

            Database::getConnection()->executeQuery(
                "UPDATE crypto SET crypto_count = 0, crypto_solo_price = 0, crypto_price = 0
                  WHERE id = '{$_SESSION['auth_id']}' AND trade = 'owned'"
            )->fetchAssociative();
        }
    }
}