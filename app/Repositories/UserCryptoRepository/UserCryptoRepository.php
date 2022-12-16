<?php

namespace App\Repositories\UserCryptoRepository;

interface UserCryptoRepository
{
    public function getTransactions(int $id,?string $symbol=null): array;
    public function getUserCrypto(int $id): array;
    public function updatePrice(array $portfolio): void;
    public function buy(float $price,string $symbol,float $count): void;
    public function sell(float $price,string $symbol,float $count): void;
    public function sendCrypto(string $symbol, float $amount, string $email): void;
}