<?php

namespace App\Repositories\UserCryptoRepository;

interface UserCryptoRepository
{
    public function getTransactions(int $id,?string $symbol=null): array;
    public function getUserCrypto(?int $id=null, ?string $symbol=null): array;
    public function updatePrice(array $portfolio): void;
    public function buy(float $price,string $symbol,float $count): void;
    public function sell(float $price,string $symbol,float $count): void;
    public function sendCrypto(string $symbol, float $amount, string $email, float $currentPrice): void;
    public function changeUserMoney(float $money): void;
    public function getUserShorts(int $id): array;
    public function closeShort(float $price,string $symbol, float $amount): void;
}