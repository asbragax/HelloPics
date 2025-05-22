<?php

namespace PicPay;

use Exception;

class Client
{
    private string $token;
    private string $baseUrl;

    public function __construct(string $token, bool $sandbox = false)
    {
        $this->token = $token;
        $this->baseUrl = $sandbox
            ? 'https://checkout-api-sandbox.picpay.com/api/v1/'
            : 'https://checkout-api.picpay.com/api/v1/';
    }

    private function request(string $method, string $endpoint, ?array $data = null): array
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-picpay-token: ' . $this->token,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);

        if ($response === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new Exception("Erro CURL: $err");
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            throw new Exception("Erro na requisição [$httpCode]: $response");
        }

        $decoded = json_decode($response, true);

        if ($decoded === null) {
            throw new Exception("Erro ao decodificar JSON: $response");
        }

        return $decoded;
    }

    public function createPayment(array $data): array
    {
        return $this->request('POST', 'payments', $data);
    }

    public function getPayment(string $paymentId): array
    {
        return $this->request('GET', "payments/{$paymentId}");
    }
}
