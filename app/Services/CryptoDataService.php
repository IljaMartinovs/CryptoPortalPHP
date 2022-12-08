<?php

namespace App\Services;

class CryptoDataService
{
    public function getCrypto(string $start, string $limit, string $convert = 'USD'): array
    {
        $parameters = [
            'start' => $start,
            'limit' => $limit,
            'convert' => $convert
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

        $response = curl_exec($curl);
        $data = json_decode($response, true);
        curl_close($curl);
        return $data;
    }
}