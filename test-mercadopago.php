<?php

require __DIR__ . '/../vendor/autoload.php';

use MercadoPago\PaymentHandler;

try {
    echo "Autoload carregado<br>";

    $handler = new PaymentHandler();

    $payment = $handler->createPixPayment(
        "Compra na HelloPics",
        20.00,
        "cliente@email.com"
    );

    echo "<img src='data:image/png;base64,{$payment['qr_code_base64']}' />";
    echo "<pre>" . print_r($payment, true) . "</pre>";

} catch (Exception $e) {
    echo "Erro geral: " . $e->getMessage();
}
