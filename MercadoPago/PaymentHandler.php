<?php

namespace MercadoPago;

use MercadoPago\Client;
use MercadoPago\Payment;

class PaymentHandler
{
    public function __construct()
    {
        new Client(); // carrega .env e inicializa SDK
    }

    public function createPixPayment(string $description, float $amount, string $payerEmail): array
    {
        $payment = new Payment();
        $payment->transaction_amount = $amount;
        $payment->description = $description;
        $payment->payment_method_id = "pix";
        $payment->payer = ["email" => $payerEmail];

        $payment->save();

        return [
            'qr_code' => $payment->point_of_interaction->transaction_data->qr_code,
            'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64,
            'payment_id' => $payment->id,
            'status' => $payment->status,
        ];
    }

    public function createCardPayment(float $amount, string $token, string $email, int $installments): array
    {
        $payment = new Payment();
        $payment->transaction_amount = $amount;
        $payment->token = $token;
        $payment->description = "Pagamento com cartão";
        $payment->installments = $installments;
        $payment->payment_method_id = "visa";
        $payment->payer = ["email" => $email];

        $payment->save();

        return [
            'status' => $payment->status,
            'status_detail' => $payment->status_detail,
            'id' => $payment->id
        ];
    }
}
