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
        $queryBuilder1 = Database::getConnection()->createQueryBuilder();

        $user = $queryBuilder
            ->select('name, email, money')
            ->from('users')
            ->where('id = ?')
            ->setParameter(0, $_SESSION['auth_id'])
            ->fetchAssociative();

        $crypto = $queryBuilder1
            ->select('crypto_name, crypto_count')
            ->from('crypto')
            ->where('id =' . $_SESSION['auth_id'])
            ->setParameter(0, $_SESSION['crypto'])
            ->fetchAssociative();

        return [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'money' => $user['money'],
            'cryptoName' => $crypto['crypto_name'],
            'cryptoCount' => $crypto['crypto_count']
        ];
    }
}