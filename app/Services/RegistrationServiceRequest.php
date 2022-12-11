<?php

namespace App\Services;

class RegistrationServiceRequest
{
    private string $name;
    private string $email;
    private string $password;
    private int $money;

    public function __construct(string $name, string $email, string $password, int $money = 0)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->money = $money;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getMoney(): int
    {
        return $this->money;
    }
}