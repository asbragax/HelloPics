<?php

require_once __DIR__ . '/vendor/autoload.php';

use PicPay\Client;
use Dotenv\Dotenv;

try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $token = $_ENV['PICPAY_TOKEN'] ?? '';
    $sandbox = filter_var($_ENV['PICPAY_SANDBOX'] ?? 'false', FILTER_VALIDATE_BOOLEAN);

    if (empty($token)) {
        throw new Exception("Token PicPay não configurado no arquivo .env");
    }

    $client = new Client($token, $sandbox);

    $payload = [
        "referenceId" => 1234,
        "callbackUrl" => "https://hellopics.gkadmin.com.br/admin/picpay/callback.php",
        "returnUrl" => "https://hellopics.gkadmin.com.br/thankyou.php",
        "value" => 150.00,
        "buyer" => [
            "firstName" => "João",
            "lastName" => "Silva",
            "document" => "12345678900",
            "email" => "joao@teste.com",
            "phone" => "+5511999999999"
        ]
    ];

    $response = $client->createPayment($payload);

    echo "Pagamento criado com sucesso:\n";
    print_r($response);

} catch (Exception $e) {
    echo "Erro geral: " . $e->getMessage() . "\n";
}
