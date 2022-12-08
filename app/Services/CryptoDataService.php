<?php

namespace App\Services;

class CryptoDataService
{
    private $results;

    public function __construct()
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

        $this->results = json_decode(curl_exec($curl));
        curl_close($curl);
    }

    public function getCrypto(): object
    {
        return $this->results;
    }
}