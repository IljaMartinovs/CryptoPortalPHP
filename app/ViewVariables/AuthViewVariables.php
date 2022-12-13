<?php

namespace App\ViewVariables;

use App\Database;

class AuthViewVariables implements ViewVariables
{
    public function getName(): string
    {
        return 'auth';
    }

    public function getValue(): array
    {
        if (!isset($_SESSION['auth_id'])) {
            return [];
        }
        $queryBuilder = Database::getConnection()->createQueryBuilder();

        $user = $queryBuilder
            ->select('name, email, money')
            ->from('users')
            ->where('id = ?')
            ->setParameter(0, $_SESSION['auth_id'])
            ->fetchAssociative();

        $ownedCrypto = Database::getConnection()->executeQuery(
            "SELECT crypto_name, crypto_count, crypto_price, crypto_solo_price, trade FROM crypto 
                 WHERE id = '{$_SESSION['auth_id']}' AND trade = 'owned'"
        )->fetchAllAssociative();

        $count = Database::getConnection()->executeQuery(
            "SELECT crypto_name, crypto_count, crypto_price, crypto_solo_price, trade, bought_time 
                 FROM crypto WHERE id = '{$_SESSION['auth_id']}'"
        )->fetchAllAssociative();

        return [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'money' => $user['money'],
            'crypto' => $count,
            'owned' => $ownedCrypto,
        ];
    }
}