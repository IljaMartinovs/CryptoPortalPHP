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
}