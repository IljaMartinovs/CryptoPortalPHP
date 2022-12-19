<?php

namespace App;

class Validation
{
    public function validate(): void
    {
        $this->validateNewName();
        $this->validateNewEmail();
        $this->validateNewPassword();
    }

    public function loginValidate(): void
    {
        $queryBuilder = Database::getConnection()->createQueryBuilder();
        $user = $queryBuilder
            ->select('*')
            ->from('users')
            ->where('email = ?')
            ->setParameter(0, $_POST['email'])
            ->fetchAssociative();

        if (!$user)
            $_SESSION['errors']['email'] = 'Invalid login email or password';

        if ($user && !password_verify($_POST['password'], $user['password']))
            $_SESSION['errors']['password'] = 'Invalid login email or password';

    }

    public function validationFailed(): bool
    {
        if (count($_SESSION['errors']) > 0)
            return true;
        return false;
    }

    private function validateNewName(): void
    {
        if (strlen($_POST['name']) < 3 || !preg_match("/^[a-zA-Z-' ]*$/", $_POST['name'])) {
            $_SESSION['errors']['name'] = 'Name must be at least 3 characters long and contains only letters';
        }
    }

    private function validateNewEmail(): void
    {
        $queryBuilder = Database::getConnection()->createQueryBuilder();
        $user = $queryBuilder
            ->select('*')
            ->from('users')
            ->where('email = ?')
            ->setParameter(0, $_POST['email'])
            ->fetchOne();

        if ($user) {
            $_SESSION['errors']['email'] = 'Invalid email address';
        }
    }

    private function validateNewPassword(): void
    {
        if (strlen($_POST['password']) < 8) {
            $_SESSION['errors']['password'] = 'Password must be at least 8 characters long';
        }
        if ($_POST['password'] !== $_POST['confirm-password']) {
            $_SESSION['errors']['password_repeat'] = 'Passwords do not match';
        }
        if (count($_SESSION['errors']) == 0)
            $_SESSION['success']['registration'] = "You successfully registered";
    }

    public function buyCryptoValidate(string $symbol, float $price, float $count): void
    {
        $price *= $count;
        $userMoney = Database::getConnection()->executeQuery("SELECT money FROM users WHERE id= '{$_SESSION['auth_id']}'")->fetchAssociative();
        if ($symbol == null)
            $_SESSION['errors']['crypto'] = 'Invalid crypto';
        if ((float)$userMoney['money'] < $price)
            $_SESSION['errors']['crypto'] = 'Not enough money';
        if (count($_SESSION['errors']['crypto']) == 0)
            $_SESSION['success']['crypto'] = "You successfully purchased $count $symbol";
    }

    public function closeShort(string $symbol, float $amount): void
    {
        $shortName = Database::getConnection()->executeQuery("SELECT crypto_name FROM crypto WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'open short'")->fetchAssociative();
        $shortAmount = Database::getConnection()->executeQuery("SELECT crypto_count FROM crypto WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'open short'")->fetchAssociative();



        if ($shortName['crypto_name'] != $symbol)
            $_SESSION['errors']['shorts'] = 'You dont have this crypto currency';

        if ((float)$shortAmount['crypto_count'] < $amount)
            $_SESSION['errors']['shorts'] = 'Not enough crypto currency';

        if (count($_SESSION['errors']['crypto']) == 0)
            $_SESSION['success']['shorts'] = "You successfully closed $amount $symbol";

    }

    public function sellCryptoValidate(string $symbol, float $count): void
    {
        $cryptoName = Database::getConnection()->executeQuery("SELECT crypto_name FROM crypto WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'owned'")->fetchAssociative();
        $cryptoAmount = Database::getConnection()->executeQuery("SELECT crypto_count FROM crypto WHERE id= '{$_SESSION['auth_id']}' AND crypto_name = '$symbol' AND trade = 'owned'")->fetchAssociative();

        if ($cryptoName['crypto_name'] != $symbol)
            $_SESSION['errors']['crypto'] = 'You dont have this crypto currency';

        if ((float)$cryptoAmount['crypto_count'] < $count)
            $_SESSION['errors']['crypto'] = 'Not enough crypto currency';

        if (count($_SESSION['errors']['crypto']) == 0)
            $_SESSION['success']['crypto'] = "You successfully sold $count $symbol";
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

        if($checkForEmail['email'] != $email)
            $_SESSION['errors']['crypto'] = "User with that e-mail doesn't exist";
        if($crypto['crypto_name'] != $symbol)
            $_SESSION['errors']['crypto'] = "You don't have $symbol";
        if($crypto['crypto_count'] < $amount)
            $_SESSION['errors']['crypto'] = "You don't have enough $symbol";
        if(!password_verify($password ,$userPassword['password'] ))
            $_SESSION['errors']['crypto'] = "Passwords don't match";
        if (count($_SESSION['errors']['crypto']) == 0)
            $_SESSION['success']['crypto'] = "You successfully send $amount $symbol";

    }
}