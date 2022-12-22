<?php

namespace App;

class CryptoValidation
{
    public function buyCryptoValidate(string $symbol, float $price, float $count): void
    {
        $price *= $count;
        $userMoney = Database::getConnection()->executeQuery(
            "SELECT money FROM users WHERE id= '{$_SESSION['auth_id']}'")->fetchAssociative();
        if ($symbol == null)
            $_SESSION['errors']['crypto'] = 'Invalid crypto';
        if ((float)$userMoney['money'] < $price)
            $_SESSION['errors']['crypto'] = 'Not enough money';
        $price = round($price,2);
        if (count($_SESSION['errors']['crypto']) == 0)
            $_SESSION['success']['crypto'] = "You successfully purchased $count $symbol for $price$";
    }

    public function closeShort(string $symbol, float $amount): void
    {
        $shortName = Database::getConnection()->executeQuery(
            "SELECT crypto_name FROM crypto WHERE id = '{$_SESSION['auth_id']}'
                                 AND crypto_name = '$symbol' AND trade = 'open short'")->fetchAssociative();
        $shortAmount = Database::getConnection()->executeQuery(
            "SELECT crypto_count FROM crypto WHERE id = '{$_SESSION['auth_id']}' 
                                  AND crypto_name = '$symbol' AND trade = 'open short'")->fetchAssociative();

        if ($shortName['crypto_name'] != $symbol)
            $_SESSION['errors']['shorts'] = 'You dont have this crypto currency';

        if ((float)$shortAmount['crypto_count'] < $amount)
            $_SESSION['errors']['shorts'] = 'Not enough crypto currency';

    }

    public function sellCryptoValidate(string $symbol, float $price, float $count): void
    {
        $cryptoName = Database::getConnection()->executeQuery(
            "SELECT crypto_name FROM crypto WHERE id = '{$_SESSION['auth_id']}' 
                                 AND crypto_name = '$symbol' AND trade = 'owned'")->fetchAssociative();
        $cryptoAmount = Database::getConnection()->executeQuery(
            "SELECT crypto_count FROM crypto WHERE id= '{$_SESSION['auth_id']}'
                                  AND crypto_name = '$symbol' AND trade = 'owned'")->fetchAssociative();

        if ($cryptoName['crypto_name'] != $symbol)
            $_SESSION['errors']['crypto'] = 'You dont have this crypto currency';
        if ((float)$cryptoAmount['crypto_count'] < $count)
            $_SESSION['errors']['crypto'] = 'Not enough crypto currency';
        $price *= $count;
        $price = round($price,2);
        if (count($_SESSION['errors']['crypto']) == 0)
            $_SESSION['success']['crypto'] = "You successfully sold $count $symbol for $price$";
    }

    public function sendCrypto(string $symbol, float $amount, string $password, string $email): void
    {
        $crypto = Database::getConnection()->executeQuery(
            "SELECT crypto_name, crypto_count FROM crypto
                    WHERE id= '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'owned'")->fetchAssociative();
        $userPassword = Database::getConnection()->executeQuery(
            "SELECT password FROM users
                    WHERE id= '{$_SESSION['auth_id']}'")->fetchAssociative();
        $checkForEmail = Database::getConnection()->executeQuery(
            "SELECT email FROM users
                    WHERE email= '$email'")->fetchAssociative();

        if ($checkForEmail['email'] != $email)
            $_SESSION['errors']['crypto'] = "User with that e-mail doesn't exist";
        if ($crypto['crypto_name'] != $symbol)
            $_SESSION['errors']['crypto'] = "You don't have $symbol";
        if ($crypto['crypto_count'] < $amount)
            $_SESSION['errors']['crypto'] = "You don't have enough $symbol";
        if (!password_verify($password, $userPassword['password']))
            $_SESSION['errors']['crypto'] = "Passwords don't match";
        if (count($_SESSION['errors']['crypto']) == 0)
            $_SESSION['success']['crypto'] = "You successfully send $amount $symbol";

    }

    public function validationFailed(): bool
    {
        if (count($_SESSION['errors']) > 0)
            return true;
        return false;
    }
}