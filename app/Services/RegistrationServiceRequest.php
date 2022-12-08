<?php

namespace App\Services;

class RegistrationServiceRequest
{
    private string $name;
    private string $email;
    private string $password;
    private string $repeatedPassword;

    public function __construct(string $name, string $email, string $password, string $repeatedPassword)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->repeatedPassword = $repeatedPassword;
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

    public function getRepeatedPassword(): string
    {
        return $this->repeatedPassword;
    }
}