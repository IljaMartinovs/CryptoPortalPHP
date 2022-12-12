<?php

namespace App\Services;

use App\Database;
use App\Models\Collection\CryptoCurrenciesCollection;
use App\Models\Collection\TransactionsCollection;
use App\Models\Transactions;
use App\Validation;
use function GuzzleHttp\Promise\all;
use function Sodium\add;

class EditService
{
    public function changeUserMoney(int $money): void
    {
        $validation = new Validation();
        $response = $validation->changeMoneyValidate($money);
        Database::getConnection()->executeQuery("UPDATE users  SET money = '{$response}' WHERE id= '{$_SESSION['auth_id']}'");
    }

    public function buyCrypto(CryptoCurrenciesCollection $cryptoCurrenciesCollection, int $count): void
    {
        $validation = new Validation();
        $response = $validation->buyCryptoValidate($cryptoCurrenciesCollection, $count);
        $symbol = $response[0];
        $soloPrice = $response[1];
        $price = $response[2];
        $trade = 'purchased';
        $owned = 'owned';
        $query = Database::getConnection()->executeQuery("SELECT * FROM crypto
         WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = '$owned'")->fetchAllAssociative();

        //INSERT into database transaction for cryptocurrency
        Database::getConnection()->executeQuery(
            "INSERT INTO crypto (id, crypto_name, crypto_count,crypto_solo_price, crypto_price, trade)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$soloPrice','$price','$trade')"
        )->fetchAllAssociative();

        //IF this cryptocurrency exist then do not put trade = owned but just owned + $count
        if (count($query) == 0) {
            Database::getConnection()->executeQuery(
                "INSERT INTO crypto (id, crypto_name, crypto_count,crypto_solo_price, crypto_price, trade)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$soloPrice','$price','$owned')"
            )->fetchAllAssociative();
        } else if (count($query) == 1) {
            Database::getConnection()->executeQuery(
                "UPDATE crypto SET crypto_count = crypto_count + '$count'
              WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'owned'"
            )->fetchAllAssociative();
        }
    }

    public function sellCrypto(CryptoCurrenciesCollection $cryptoCurrenciesCollection, int $count): void
    {
        $validation = new Validation();
        $response = $validation->sellCryptoValidate($cryptoCurrenciesCollection, $count);
        $newAmount = $response[0];
        $newMoney = $response[1];
        $symbol = $response[2];
        $price = $response[3];
        $trade = 'sold';

        if ($newAmount > 0) {
            Database::getConnection()->executeQuery(
                "INSERT INTO crypto (id, crypto_name, crypto_count, crypto_price, trade)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$price','$trade')"
            )->fetchAllAssociative();

            Database::getConnection()->executeQuery(
                "UPDATE crypto SET crypto_count = crypto_count - '$count'
              WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'owned'"
            )->fetchAllAssociative();
            (new EditService())->changeUserMoney($newMoney);

        } else if ($newAmount == 0) {
            Database::getConnection()->executeQuery(
                "UPDATE crypto SET crypto_count = '$newAmount', crypto_price = '$newAmount'
              WHERE crypto_name = '$symbol' and id = '{$_SESSION['auth_id']}'"
            )->fetchAssociative();
//            Database::getConnection()->executeQuery(
//                "UPDATE crypto SET crypto_count = '$newAmount', crypto_price = crypto_price -'$newMoney'
//              WHERE crypto_name = '$symbol' and id = '{$_SESSION['auth_id']}'"
//            )->fetchAssociative();
            Database::getConnection()->executeQuery(
                "DELETE FROM crypto WHERE crypto_count = 0 AND id= '{$_SESSION['auth_id']}' AND trade = 'owned'")->fetchAssociative();
        }
    }
}