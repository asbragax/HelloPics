<?php
// Inclui o autoload gerado pelo Composer
require dirname(__FILE__) . '/vendor/autoload.php';

echo "Autoload carregado\n";

use PicPay\Client;
use PicPay\Payment;
use Dotenv\Dotenv;

try {
    // Carrega as variáveis do .env na raiz do projeto
    $dotenv = Dotenv::createImmutable(dirname(__FILE__));
    $dotenv->load();

    // Pega as configurações do .env
    $token = $_ENV['PICPAY_TOKEN'] ?? '';
    $sandbox = filter_var($_ENV['PICPAY_SANDBOX'] ?? 'false', FILTER_VALIDATE_BOOLEAN);

    if (empty($token)) {
        throw new Exception("Token PicPay não configurado no arquivo .env");
    }

    // Cria cliente PicPay e instância de pagamento
    $client = new Client($token, sandbox: $sandbox);
    $payment = new Payment($client);

    // Dados do pagamento
    $payload = [
        'referenceId' => uniqid(),
        'callbackUrl' => 'https://hellopics.gkadmin.com.br/admin/picpay/callback.php',
        'returnUrl' => 'https://hellopics.gkadmin.com.br/thankyou.php',
        'value' => 120.50,
        'buyer' => [
            'firstName' => 'João',
            'lastName' => 'Silva',
            'document' => '12345678900',
            'email' => 'joao@teste.com',
            'phone' => '+5511999999999'
        ]
    ];

    // Cria pagamento e captura resposta
    try {
        $res = $payment->create($payload);
        echo "<pre>";
        print_r($res);
        echo "</pre>";

        if (isset($res['paymentUrl'])) {
            echo "<a href='{$res['paymentUrl']}' target='_blank'>Ir para o pagamento</a>";
        } else {
            echo "URL de pagamento não encontrada na resposta.";
        }
    } catch (\PicPay\Exceptions\PicPayException $e) {
        echo "Erro ao criar pagamento: " . $e->getMessage();
    }
} catch (\Throwable $th) {
    echo "Erro geral: " . $th->getMessage();
    echo "<pre>";
    print_r($th);
    echo "</pre>";
}
