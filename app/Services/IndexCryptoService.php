<?php

namespace App\Services;

use App\CryptoCollection;
use App\Repositories\CryptoApiCryptoRepository;
use App\Repositories\CryptoRepository;

class IndexCryptoService
{
    private CryptoRepository $cryptoRepository;

    public function __construct()
    {
        $this->cryptoRepository = new CryptoApiCryptoRepository();
    }

    public function execute(): CryptoCollection
    {
        return $this->cryptoRepository->getCrypto(1, 12);
    }
}