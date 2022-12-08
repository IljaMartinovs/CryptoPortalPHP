<?php

namespace App;

class Validation
{
    public static function validateName(string $name): void
    {
        if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
            $_SESSION['error'] = "name contains wrong symbols";
        }
    }

    public static function validateEmail(string $email): void
    {
        foreach (Database::getConnection()->fetchAllAssociative("SELECT email FROM users") as $user) {
            if ($user['email'] == $email)
                $_SESSION['error'] = "email you are trying to register already exists";
        }
    }

    public static function validatePassword(string $passwordNew, string $passwordNewConfirm, ?string $passwordCurrent = null): void
    {
        $number = preg_match('@[0-9]@', $passwordNew);
        $uppercase = preg_match('@[A-Z]@', $passwordNew);
        $lowercase = preg_match('@[a-z]@', $passwordNew);

        if (strlen($passwordNew) < 8 || !$number || !$uppercase || !$lowercase)
            $_SESSION['error'] = "password must be at least 8 characters in length and must contain at least one number, one upper case letter and one lower case letter";
        if ($passwordNew != $passwordNewConfirm)
            $_SESSION['error'] = "passwords are not equal!";
        if ($passwordCurrent != null) {
            if ($_SESSION['password'] != $passwordCurrent)
                $_SESSION['error'] = "wrong current password!";
        }
    }

    public static function validateUser(string $email, string $password): void
    {
        foreach (Database::getConnection()->fetchAllAssociative("SELECT id,email,password,name FROM users") as $user) {
            if ($user['email'] == $email && $user['password'] == $password) {
                $_SESSION['name'] = $user['name'];
                $_SESSION['id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['password'] = $user['password'];
            }
        }
        if ($_SESSION == null)
            $_SESSION['error'] = "wrong email or password";
    }
}