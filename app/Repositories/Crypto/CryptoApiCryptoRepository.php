<?php

namespace App\Repositories\Crypto;

use App\Models\Collections\CryptoCollection;
use App\Models\Crypto;

class CryptoApiCryptoRepository implements CryptoRepository
{
    public function getCrypto(): CryptoCollection
    {
        $parameters = [
            'start' => '1',
            'limit' => '10',
            'convert' => 'USD'
        ];

        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY: ' . $_ENV['APIKEY']
        ];

        $qs = http_build_query($parameters);
        $request = "{$_ENV['URL']}?{$qs}";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => 1
        ));

        $cryptoResponse = json_decode(curl_exec($curl));
        curl_close($curl);

        $cryptos = new CryptoCollection();

        foreach ($cryptoResponse->data as $coin) {
            $cryptos->addCrypto(new Crypto(
                $coin->name,
                $coin->symbol,
                $coin->quote->USD->price,
                $coin->quote->USD->percent_change_24h
            ));
        }
        return $cryptos;
    }
}