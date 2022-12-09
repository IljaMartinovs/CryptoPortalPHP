<?php

namespace App\Services;

use App\Database;

class LoginService
{
    public function execute(): void
    {
        $queryBuilder = Database::getConnection()->createQueryBuilder();
        $user = $queryBuilder
            ->select('*')
            ->from('users')
            ->where('email = ?')
            ->setParameter(0, $_POST['email'])
            ->fetchAssociative();

        $_SESSION["auth_id"] = $user['id'];
    }
}