<?php

namespace App\Repositories\User;

use App\Database;
use App\Services\RegistrationServiceRequest;

class MySQLUserRepository implements UserRepository
{
    public function add(RegistrationServiceRequest $request): void
    {
        Database::getConnection()->insert(
            'users', [
            'name' => $request->getName(),
            'email' => filter_var($request->getEmail(), FILTER_SANITIZE_EMAIL),
            'password' => password_hash($request->getPassword(), PASSWORD_DEFAULT),
            'money' => $request->getMoney()
        ]);
    }

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