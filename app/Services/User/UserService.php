<?php

namespace App\Services\User;

use App\Repositories\UserCryptoRepository\UserCryptoRepository;

class UserService
{
    private UserCryptoRepository $userCryptoRepository;

    public function __construct(UserCryptoRepository $userCryptoRepository)
    {
        $this->userCryptoRepository = $userCryptoRepository;
    }

    public function updatePrice(array $portfolio): void
    {
         $this->userCryptoRepository->updatePrice($portfolio);
    }

    public function getUserCrypto(?int $id=null, ?string $symbol=null): array
    {
        return $this->userCryptoRepository->getUserCrypto($id,$symbol);
    }

    public function getUserShorts(int $id): array
    {
        return $this->userCryptoRepository->getUserShorts($id);
    }

    public function getTransactions(int $id,?string $symbol=null): array
    {
        return $this->userCryptoRepository->getTransactions($id,$symbol);
    }

    public function sendCrypto(string $symbol, float $amount, string $email, float $currentPrice): void
    {
        $this->userCryptoRepository->sendCrypto($symbol,$amount, $email,$currentPrice);
    }

    public function changeUserMoney(float $money): void
    {
        $this->userCryptoRepository->changeUserMoney($money);
    }
}