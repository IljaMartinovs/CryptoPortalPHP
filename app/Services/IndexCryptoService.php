<?php

namespace App\Services;

use App\Models\Collections\CryptoCollection;
use App\Repositories\Crypto\CryptoApiCryptoRepository;
use App\Repositories\Crypto\CryptoRepository;

class IndexCryptoService
{
    private CryptoRepository $cryptoRepository;

    public function __construct()
    {
        $this->cryptoRepository = new CryptoApiCryptoRepository();
    }

    public function execute(): CryptoCollection
    {
        return $this->cryptoRepository->getCrypto();
    }
}