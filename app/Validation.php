<?php

namespace App;

use App\Models\Collection\CryptoCurrenciesCollection;
use App\Services\EditService;

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

        if (!$user) {
            $_SESSION['errors']['email'] = 'Invalid login email or password';
        }

        if ($user && !password_verify($_POST['password'], $user['password'])) {
            $_SESSION['errors']['password'] = 'Invalid login email or password';
        }
    }

    public function validationFailed(): bool
    {
        return count($_SESSION['errors']) > 0;
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
    }

    public function buyCryptoValidate(CryptoCurrenciesCollection $cryptoCurrenciesCollection, int $count): array // void
    {
        foreach ($cryptoCurrenciesCollection->all() as $crypto) {
            (int)$price = $crypto->getPrice();
            $symbol = $crypto->getSymbols();
        }
        $soloPrice = $price;
        $price *= $count;
        $userMoney = Database::getConnection()->executeQuery("SELECT money FROM users WHERE id= '{$_SESSION['auth_id']}'")->fetchAssociative();
        if ($symbol != null) {
            if ($userMoney['money'] >= $price) {
                (new EditService())->changeUserMoney(-$price);
                return [$symbol, (int)$soloPrice, $price];
            } else {
                $_SESSION['errors']['crypto'] = 'Not enough money';
            }
        } else $_SESSION['errors']['crypto'] = 'Invalid crypto';
        return [];
    }

    public function sellCryptoValidate(CryptoCurrenciesCollection $cryptoCurrenciesCollection, int $count): array // void
    {
        foreach ($cryptoCurrenciesCollection->all() as $crypto) {
            (int)$price = $crypto->getPrice();
            $symbol = $crypto->getSymbols();
        }

        $cryptoName = Database::getConnection()->executeQuery("SELECT crypto_name FROM crypto WHERE id = '{$_SESSION['auth_id']}' AND crypto_name = '$symbol'")->fetchAssociative();
        $cryptoAmount = Database::getConnection()->executeQuery("SELECT crypto_count FROM crypto WHERE id= '{$_SESSION['auth_id']}' AND crypto_name = '$symbol'")->fetchAssociative();

        $newMoney = $count * $price;
        if ($cryptoName['crypto_name'] == $symbol) {
            if ((int)$cryptoAmount['crypto_count'] >= $count) {
                $newAmount = (int)$cryptoAmount['crypto_count'] - $count;
                (new EditService())->changeUserMoney($newMoney);
                return [$newAmount, $newMoney, $symbol,$price];
            } else
                $_SESSION['errors']['crypto'] = 'Not enough crypto currency';
        } else
            $_SESSION['errors']['crypto'] = 'You dont have this crypto currency';
        return [];
    }

    public function changeMoneyValidate(int $money): string
    {
        $dbSum = Database::getConnection()->executeQuery("SELECT money FROM users WHERE id= '{$_SESSION['auth_id']}'")->fetchAssociative();
        return (int)$dbSum['money'] + $money;
    }
}