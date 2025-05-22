<?php
// callback.php

// Define o fuso horário
date_default_timezone_set('America/Sao_Paulo');

// Caminho para log
$logFile = __DIR__ . '../../../callback-logs.txt';

// Lê o conteúdo JSON enviado pelo PicPay
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Log da requisição recebida
file_put_contents($logFile, date('Y-m-d H:i:s') . " | Callback recebido: " . $input . PHP_EOL, FILE_APPEND);

// Valida se os dados essenciais estão presentes
if (!isset($data['referenceId']) || !isset($data['status'])) {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " | Dados incompletos no callback" . PHP_EOL, FILE_APPEND);
    http_response_code(400); // Bad Request
    exit('Dados incompletos');
}

// Sanitiza entrada
$referenceId = preg_replace('/[^a-zA-Z0-9\-]/', '', $data['referenceId']);
$status = $data['status']; // Exemplo: 'paid', 'cancelled', 'expired'
$authorizationId = $data['authorizationId'] ?? null;

// Aqui você atualizaria o status do pedido no banco
// Exemplo fictício:
try {
    // $pdo = new PDO(...);
    // $stmt = $pdo->prepare("UPDATE pedidos SET status = ?, atualizado_em = NOW() WHERE referencia = ?");
    // $stmt->execute([$status, $referenceId]);

    file_put_contents($logFile, date('Y-m-d H:i:s') . " | Pedido atualizado: $referenceId -> $status" . PHP_EOL, FILE_APPEND);
} catch (Exception $e) {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " | ERRO ao atualizar pedido: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
    http_response_code(500); // Erro interno
    exit('Erro interno');
}

// Retorna 200 para informar que recebeu corretamente
http_response_code(200);
echo 'OK';
