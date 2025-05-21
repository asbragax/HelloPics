<?php

namespace PicPay;

use PicPay\Exceptions\PicPayException;

class Client
{
    private string $token;
    private string $baseUrl;

    public function __construct(string $token, bool $sandbox = false)
    {
        $this->token = $token;
        $this->baseUrl = $sandbox 
            ? 'https://sandbox.picpay.com/ecommerce/public' 
            : 'https://appws.picpay.com/ecommerce/public';
    }

    public function post(string $endpoint, array $data): array
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-picpay-token: ' . $this->token
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            throw new PicPayException("Erro na requisição [$httpCode]: $response");
        }

        return json_decode($response, true);
    }

    public function get(string $endpoint): array
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-picpay-token: ' . $this->token
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            throw new PicPayException("Erro na requisição [$httpCode]: $response");
        }

        return json_decode($response, true);
    }
}
