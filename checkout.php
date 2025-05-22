<?php

require __DIR__ . '/vendor/autoload.php';

use MercadoPago\Client;
use MercadoPago\PaymentHandler;

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $token = $_POST['token'];
        $email = $_POST['email'];
        $amount = floatval($_POST['amount']);
        $installments = intval($_POST['installments']);

        $handler = new PaymentHandler();
        $result = $handler->createCardPayment($amount, $token, $email, $installments);

        $msg = "Pagamento criado com sucesso! Status: " . $result['status'] . "<br>ID: " . $result['id'];

        // Salvar no .txt (substitua por lógica de banco depois)
        file_put_contents(
            __DIR__ . '/pagamentos.txt',
            date('c') . " - PAGAMENTO CARTÃO - ID: {$result['id']} - STATUS: {$result['status']}\n",
            FILE_APPEND
        );

    } catch (Exception $e) {
        $msg = "Erro ao processar pagamento: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout Transparente</title>
    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>
<body>
    <h1>Pagamento com Cartão de Crédito</h1>

    <?php if ($msg): ?>
        <p><strong><?= $msg ?></strong></p>
    <?php endif; ?>

    <form id="paymentForm" method="POST">
        <input type="hidden" name="token" id="token" />
        <input type="hidden" name="installments" value="1" />

        <label>
            Email do pagador:
            <input type="email" name="email" value="cliente@email.com" required />
        </label><br><br>

        <label>
            Valor (R$):
            <input type="number" name="amount" step="0.01" value="20.00" required />
        </label><br><br>

        <label>
            Número do Cartão:
            <input type="text" id="cardNumber" data-checkout="cardNumber" placeholder="4242 4242 4242 4242" />
        </label><br><br>

        <label>
            Data de validade (MM/YY):
            <input type="text" id="cardExpiration" placeholder="12/25" />
        </label><br><br>

        <label>
            CVV:
            <input type="text" id="securityCode" data-checkout="securityCode" placeholder="123" />
        </label><br><br>

        <label>
            Nome no cartão:
            <input type="text" id="cardholderName" data-checkout="cardholderName" placeholder="JOÃO DA SILVA" />
        </label><br><br>

        <label>
            Documento (CPF):
            <input type="text" id="docNumber" data-checkout="docNumber" placeholder="12345678909" />
        </label><br><br>

        <button type="submit">Pagar</button>
    </form>

    <script>
        const mp = new MercadoPago("<?= $_ENV['MERCADO_PAGO_PUBLIC_KEY'] ?>");

        const cardForm = mp.cardForm({
            amount: "20.00",
            autoMount: true,
            form: {
                id: "paymentForm",
                cardholderName: { id: "cardholderName", placeholder: "Titular do cartão" },
                cardholderEmail: { id: "email", placeholder: "email@email.com" },
                cardNumber: { id: "cardNumber", placeholder: "Número do cartão" },
                cardExpirationDate: { id: "cardExpiration", placeholder: "MM/YY" },
                securityCode: { id: "securityCode", placeholder: "123" },
                identificationType: { id: "docType", placeholder: "CPF" },
                identificationNumber: { id: "docNumber", placeholder: "12345678909" },
            },
            callbacks: {
                onFormMounted: error => {
                    if (error) return console.warn("Erro ao montar formulário: ", error);
                },
                onSubmit: event => {
                    event.preventDefault();
                    cardForm.createCardToken().then(result => {
                        if (result.error) {
                            alert("Erro ao gerar token: " + result.error.message);
                            return;
                        }
                        document.getElementById('token').value = result.token;
                        document.getElementById('paymentForm').submit();
                    });
                }
            }
        });
    </script>
</body>
</html>
