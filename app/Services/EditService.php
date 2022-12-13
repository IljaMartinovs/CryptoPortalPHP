<?php

namespace App\Services;

use App\Database;
use App\Models\Collection\CryptoCurrenciesCollection;
use App\Redirect;
use App\Validation;

class EditService
{
    public function changeUserMoney(float $money): void
    {
        $validation = new Validation();
        $response = $validation->changeMoneyValidate($money);
        Database::getConnection()->executeQuery("UPDATE users  SET money = '{$response}' WHERE id= '{$_SESSION['auth_id']}'");
    }

    public function buyCrypto(CryptoCurrenciesCollection $cryptoCurrenciesCollection, float $count): Redirect
    {
        $validation = new Validation();
        $validation->buyCryptoValidate($cryptoCurrenciesCollection, $count);
        if ($validation->validationFailed()) {
            return new Redirect('/');
        }

        foreach ($cryptoCurrenciesCollection->all() as $crypto) {
            (int)$price = $crypto->getPrice();
            $symbol = $crypto->getSymbols();
        }

        $soloPrice = $price;
        $price *= $count;
        $trade = 'purchased';
        $owned = 'owned';
        $currentDate = date("m/d/Y H:i:s");
        $query = Database::getConnection()->executeQuery("SELECT * FROM crypto
         WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = '$owned'")->fetchAllAssociative();

        //INSERT into database transaction for cryptocurrency
        Database::getConnection()->executeQuery(
            "INSERT INTO crypto (id, crypto_name, crypto_count,crypto_solo_price, crypto_price, trade, bought_time)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$soloPrice', CAST($price AS FLOAT),'$trade','$currentDate')"
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
              WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = '$owned'"
            )->fetchAllAssociative();
        }
        (new EditService())->changeUserMoney(-$price);
        return new Redirect('/');
    }

    public function sellCrypto(CryptoCurrenciesCollection $cryptoCurrenciesCollection, float $count): Redirect
    {
        $validation = new Validation();
        $validation->sellCryptoValidate($cryptoCurrenciesCollection, $count);
        if ($validation->validationFailed()) {
            return new Redirect('/');
        }

        foreach ($cryptoCurrenciesCollection->all() as $crypto) {
            (int)$price = $crypto->getPrice();
            $symbol = $crypto->getSymbols();
        }
        $cryptoAmount = Database::getConnection()->executeQuery("SELECT crypto_count FROM crypto WHERE id= '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'owned'")->fetchAssociative();
        $newMoney = $price * $count;
        $newAmount = $cryptoAmount['crypto_count'] - $count;

        $trade = 'sold';
        $currentDate = date("m/d/Y H:i:s");
        // IF WANT TO SELL NOT ALL CRYPTOCURRENCY

        if ($newAmount > 0) {
            Database::getConnection()->executeQuery(
                "INSERT INTO crypto (id, crypto_name, crypto_count, crypto_solo_price, crypto_price, trade, bought_time)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$price','$newMoney','$trade', '$currentDate')"
            )->fetchAllAssociative();

            Database::getConnection()->executeQuery(
                "UPDATE crypto SET crypto_count = crypto_count - '$count', crypto_price = crypto_price - '$newMoney'
              WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'owned'"
            )->fetchAllAssociative();

            // IF WANT TO SELL  ALL CRYPTOCURRENCY
        } else if ($newAmount == 0 || $newAmount == null) {
            Database::getConnection()->executeQuery(
                "INSERT INTO crypto (id, crypto_name, crypto_count, crypto_solo_price, crypto_price,  trade, bought_time)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$price','$newMoney','$trade', '$currentDate')"
            )->fetchAllAssociative();
//            Database::getConnection()->executeQuery("DELETE FROM crypto WHERE id = 18")->fetchAllAssociative();
//            die;
            Database::getConnection()->executeQuery(
                "UPDATE crypto SET crypto_count = 0, crypto_solo_price = 0, crypto_price = 0
                  WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'owned'"
            )->fetchAssociative();

            $result = Database::getConnection()->executeQuery(
                "SELECT * FROM crypto WHERE crypto_count = 0 AND trade = 'owned'"
            )->fetchAllAssociative();

            if (count($result) != 0) {
                Database::getConnection()->executeQuery(
                    "DELETE FROM crypto WHERE crypto_count = 0 AND trade = 'owned'"
                )->fetchAllAssociative();
            }
        }
        (new EditService())->changeUserMoney($newMoney);
        return new Redirect('/');
    }
}