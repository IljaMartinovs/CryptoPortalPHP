<?php

namespace App\Repositories\UserCryptoRepository;

interface UserCryptoRepository
{
    public function buy(float $price,string $symbol,float $count): void;
    public function sell(float $price,string $symbol,float $count): void;
}