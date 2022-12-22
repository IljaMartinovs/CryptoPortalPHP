<?php

namespace App\Repositories\UserCryptoRepository;

use App\Database;


class MySQLCryptoRepository implements UserCryptoRepository
{
    public function changeUserMoney(float $money): void
    {
        $dbSum = Database::getConnection()->executeQuery("SELECT money FROM users WHERE id= '{$_SESSION['auth_id']}'")->fetchAssociative();
        $money = (float)$dbSum['money'] + $money;
        Database::getConnection()->executeQuery("UPDATE users  SET money = '{$money}' WHERE id= '{$_SESSION['auth_id']}'");
    }

    public function buy(float $price, string $symbol, float $count): void
    {
//        Database::getConnection()->executeQuery("DELETE FROM crypto WHERE id = 18")->fetchAllAssociative();
//        die;
        $soloPrice = $price;
        $price *= $count;
        $trade = 'purchased';
        $owned = 'owned';

        //GET QUERY AND CHECK IF OWNED WITH THIS CRYPTO EXIST
        $query = Database::getConnection()->executeQuery("SELECT * FROM crypto
         WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = '$owned'")->fetchAllAssociative();

        // IF NOT EXIST ADD TO DATABASE
        if (count($query) == 0) {
            Database::getConnection()->executeQuery(
                "INSERT INTO crypto (id, crypto_name, crypto_count,crypto_solo_price, crypto_price, trade, current_price)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$soloPrice','$price','$owned','$soloPrice')"
            )->fetchAllAssociative();

        } else if (count($query) == 1) {

            //SELECT PRICE AND COUNT FROM CRYPTO OWNED WITH SAME SYMBOL
            $ownedCryptoInfo = Database::getConnection()->executeQuery(
                "SELECT crypto_price, crypto_count FROM crypto
                     WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol'  AND trade = '$owned'"
            )->fetchAllAssociative();

            $total_spent = $ownedCryptoInfo[0]['crypto_price'] + $price;
            $total_crypto = $ownedCryptoInfo[0]['crypto_count'] + $count;
            $average_bought_value = $total_spent / $total_crypto;

            //UPDATE CRYPTO COUNT IN DATABASE
            Database::getConnection()->executeQuery(
                "UPDATE crypto SET crypto_count = '$total_crypto'
              WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = '$owned'"
            )->fetchAllAssociative();

            //UPDATE AVERAGE BOUGHT PRICE IN DATABASE
            Database::getConnection()->executeQuery(
                "UPDATE crypto SET  crypto_price = '$average_bought_value'
              WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = '$owned'"
            )->fetchAllAssociative();

        }
        //ADD TRANSACTION
        Database::getConnection()->executeQuery(
            "INSERT INTO transactions (id, name, count, price, transaction)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$price','$trade')"
        )->fetchAllAssociative();

        //SUBTRACT MONEY FROM USER
        $this->changeUserMoney(-$price);

    }

    public function sell(float $price, string $symbol, float $count): void
    {
        //OWNED CRYPTO AMOUNT AND PRICE
        $cryptoAmount = Database::getConnection()->executeQuery("SELECT crypto_count,crypto_price FROM crypto 
                    WHERE id= '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'owned'")->fetchAssociative();
        $newMoney = $price * $count;
        $newAmount = $cryptoAmount['crypto_count'] - $count;
        $trade = 'sold';


        $current_average_price =
            ($cryptoAmount['crypto_price'] - $newMoney) / ($cryptoAmount['crypto_count'] - $count);

        if ($newAmount > 0) {
            //UPDATE CRYPTO OWNED PRICE
            Database::getConnection()->executeQuery(
                "UPDATE crypto SET crypto_count = crypto_count - '$count', crypto_price = '$current_average_price'
              WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'owned'"
            )->fetchAllAssociative();

        } else if ($newAmount == 0 || $newAmount == null) {
            Database::getConnection()->executeQuery(
                "DELETE FROM crypto WHERE crypto_name = '$symbol' and trade = 'owned'"
            )->fetchAllAssociative();
        }

        //INSERT TRANSACTION INTO TRANSACTIONS
        Database::getConnection()->executeQuery(
            "INSERT INTO transactions (id, name, count, price, transaction)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$newMoney','$trade')"
        )->fetchAllAssociative();

        $this->changeUserMoney($newMoney);
    }

    public function sellShort(float $price, string $symbol, float $count): void
    {
        $soloPrice = $price;
        $price *= $count;

        //GET QUERY AND CHECK IF OWNED WITH THIS CRYPTO EXIST
        $query = Database::getConnection()->executeQuery("SELECT * FROM crypto
         WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'open short'")->fetchAllAssociative();

        // IF NOT EXIST ADD TO DATABASE
        if (count($query) == 0) {
            Database::getConnection()->executeQuery(
                "INSERT INTO crypto (id, crypto_name, crypto_count,crypto_solo_price, crypto_price, trade, current_price)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$soloPrice','$price','open short','$soloPrice')"
            )->fetchAllAssociative();

        } else if (count($query) == 1) {

            //SELECT PRICE AND COUNT FROM  OPEN SHORT WITH SAME SYMBOL
            $ownedShortInfo = Database::getConnection()->executeQuery(
                "SELECT crypto_price, crypto_count FROM crypto
                     WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol'  AND trade = 'open short'"
            )->fetchAllAssociative();

            $total_spent = $ownedShortInfo[0]['crypto_price'] + $price;
            $total_short = $ownedShortInfo[0]['crypto_count'] + $count;

            //UPDATE CRYPTO COUNT IN DATABASE
            Database::getConnection()->executeQuery(
                "UPDATE crypto SET crypto_count = '$total_short'
              WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'open short'"
            )->fetchAllAssociative();

            //UPDATE AVERAGE BOUGHT PRICE IN DATABASE
            Database::getConnection()->executeQuery(
                "UPDATE crypto SET  crypto_price = '$total_spent'
              WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'open short'"
            )->fetchAllAssociative();

        }

        //ADD TRANSACTION
        Database::getConnection()->executeQuery(
            "INSERT INTO transactions (id, name, count, price, transaction)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$count','$price','open short')"
        )->fetchAllAssociative();

        $this->changeUserMoney($price);
    }

    public function getUserShorts(int $id): array
    {
        return Database::getConnection()->executeQuery(
            "SELECT crypto_name, crypto_count, crypto_price, crypto_solo_price, current_price FROM crypto 
                 WHERE id = '{$_SESSION['auth_id']}' and trade = 'open short'"
        )->fetchAllAssociative();
    }

    public function closeShort(float $price, string $symbol, float $amount): void
    {
        //SELECT PRICE AND COUNT FROM  OPEN SHORT WITH SAME SYMBOL
        $ownedShort = Database::getConnection()->executeQuery(
            "SELECT crypto_count,crypto_solo_price,crypto_price,current_price FROM crypto
              WHERE id= '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'open short'")->fetchAssociative();

        $boughtPriceForAllShorts = $ownedShort['crypto_solo_price']* $amount;
        $userShortAmount = $ownedShort['crypto_count'];
        $currentPrice = $ownedShort['current_price'];
        $soloPrice = $ownedShort['crypto_solo_price'];
        $price = $currentPrice * $amount;
        $earned = ($currentPrice - $soloPrice)*$amount;

        if ($userShortAmount - $amount == 0) {
            Database::getConnection()->executeQuery(
                "DELETE FROM crypto WHERE crypto_name = '$symbol' and trade = 'open short'"
            )->fetchAllAssociative();

        } else {
            //UPDATE CRYPTO COUNT IN DATABASE
            Database::getConnection()->executeQuery(
                "UPDATE crypto SET crypto_count = crypto_count-'$amount'
              WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'open short'"
            )->fetchAllAssociative();

            //UPDATE AVERAGE BOUGHT PRICE IN DATABASE
            Database::getConnection()->executeQuery(
                "UPDATE crypto SET  crypto_price = crypto_price-'$price'
              WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'open short'"
            )->fetchAllAssociative();
        }

        //ADD TRANSACTION
        Database::getConnection()->executeQuery(
            "INSERT INTO transactions (id, name, count, price, transaction)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$amount','$price','close short')"
        )->fetchAllAssociative();

        if (-$earned > 0)
            $_SESSION['success']['shorts'] = 'You successfully closed ' . $symbol . ' and earned ' . round($earned, 3) . '$';
        else
            $_SESSION['errors']['shorts'] = 'You successfully closed ' . $symbol . ' and lost ' . round($earned, 3) . '$';
        $this->changeUserMoney(-$boughtPriceForAllShorts - $earned);
    }

    public function updatePrice(array $portfolio): void
    {
        foreach ($portfolio as $item) {
            $price = $item->getPrice();
            $symbol = $item->getSymbols();

            Database::getConnection()->executeQuery(
                "UPDATE crypto SET current_price = '$price'
              WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol'"
            )->fetchAllAssociative();
        }
    }

    public function getUserCrypto(?int $id=null, ?string $symbol=null): array
    {
        if($symbol != null)
            return Database::getConnection()->executeQuery(
                "SELECT crypto_name, crypto_count, crypto_price, crypto_solo_price, current_price FROM crypto 
                 WHERE id = '{$_SESSION['auth_id']}' and crypto_name = '$symbol' and trade = 'owned'"
            )->fetchAllAssociative();

        return Database::getConnection()->executeQuery(
            "SELECT crypto_name, crypto_count, crypto_price, crypto_solo_price, current_price FROM crypto 
                 WHERE id = '{$_SESSION['auth_id']}' and trade = 'owned'"
        )->fetchAllAssociative();
    }

    public function getTransactions(int $id, ?string $symbol = null): array
    {
        if ($symbol != null)
            return Database::getConnection()->executeQuery(
                "SELECT name, count, price, transaction, time
                 FROM transactions WHERE id = '{$_SESSION['auth_id']}' and name = '$symbol'"
            )->fetchAllAssociative();
        return Database::getConnection()->executeQuery(
            "SELECT name, count, price, transaction, time
                 FROM transactions WHERE id = '{$_SESSION['auth_id']}'"
        )->fetchAllAssociative();
    }

    public function sendCrypto(string $symbol, float $amount, string $email, float $currentPrice): void
    {
        //GET USER OWNED INFO ABOUT THIS SYMBOL CRYPTO
        $get = Database::getConnection()->executeQuery(
            "SELECT crypto_name,crypto_count,current_price FROM crypto
                 WHERE id ='{$_SESSION['auth_id']}' AND crypto_name = '$symbol'"
        )->fetchAllAssociative();

        $price = $get[0]['current_price'] * $get[0]['crypto_count'] - $get[0]['current_price'] * $amount;
        $price = $get[0]['current_price'] * $get[0]['crypto_count'] - $price;

        // ADD TRANSACTION TO SEND
        Database::getConnection()->executeQuery(
            "INSERT INTO transactions (id, name, count, price, transaction)
                 VALUES ('{$_SESSION['auth_id']}', '$symbol', '$amount','$price','sent')"
        )->fetchAllAssociative();

        //GET ID WHERE email = $email;
        $userEmail = Database::getConnection()->executeQuery(
            "SELECT id FROM users
                 WHERE email = '$email'"
        )->fetchAllAssociative();
        $userId = $userEmail[0]['id'];

        // ADD TRANSACTION TO GET
        Database::getConnection()->executeQuery(
            "INSERT INTO transactions (id, name, count, price, transaction)
                 VALUES ('$userId', '$symbol', '$amount','$price','got')"
        )->fetchAllAssociative();

        //CHECK IF THIS EMAIL HAVE OWNED THIS CRYPTO
        $query = Database::getConnection()->executeQuery("SELECT * FROM crypto
         WHERE id = '$userId' AND crypto_name = '$symbol' AND trade = 'owned'")->fetchAllAssociative();

        // IF NOT SET THIS CRYPTO WITH TRADE OWNED
        if (count($query) == 0) {
            Database::getConnection()->executeQuery(
                "INSERT INTO crypto (id, crypto_name, crypto_count,crypto_solo_price, crypto_price, trade)
                 VALUES ('$userId', '$symbol', '$amount','$currentPrice','$price','owned')"
            )->fetchAllAssociative();
        } // IF IS UPDATE CRYPTO
        else {
            //UPDATE CRYPTO DATABASE
            Database::getConnection()->executeQuery(
                "UPDATE crypto SET crypto_count = crypto_count + '$amount', crypto_price = crypto_price + '$price'
              WHERE id = '$userId'"
            )->fetchAllAssociative();
        }

        // SUBTRACT FROM ME CRYPTO
        Database::getConnection()->executeQuery(
            "UPDATE crypto SET crypto_count = crypto_count - '$amount', crypto_price = crypto_price - '$price'
              WHERE  id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol'"
        )->fetchAllAssociative();

    }
}