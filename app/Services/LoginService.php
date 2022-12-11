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
        $idFromUsers = Database::getConnection()->executeQuery("SELECT id FROM users WHERE id = '{$_SESSION['auth_id']}'")->fetchAssociative();
        $searchInCrypto = Database::getConnection()->executeQuery("SELECT id FROM crypto WHERE id = '{$idFromUsers['id']}'")->fetchAssociative();
        if ($idFromUsers['id'] != $searchInCrypto['id']) {
            Database::getConnection()->insert(
                'crypto', [
                'id' => $idFromUsers['id']]);
        }
    }
}