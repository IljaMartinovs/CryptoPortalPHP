<?php

namespace App\Services;
use App\Database;

class EditService
{
    public function changeUserMoney(float $money): void
    {
        $dbSum = Database::getConnection()->executeQuery("SELECT money FROM users WHERE id= '{$_SESSION['auth_id']}'")->fetchAssociative();
        $money = (float)$dbSum['money'] + $money;
        Database::getConnection()->executeQuery("UPDATE users  SET money = '{$money}' WHERE id= '{$_SESSION['auth_id']}'");
    }
}