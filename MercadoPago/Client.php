<?php

namespace MercadoPago;

use MercadoPago\SDK;
use MercadoPago\Item;
use MercadoPago\Payer;
use MercadoPago\Preference;
use Dotenv\Dotenv;

class Client
{
     public function __construct()
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        SDK::setAccessToken($_ENV['MERCADO_PAGO_ACCESS_TOKEN']);
    }

    public function criarPreferencia(array $produto, array $pagador): string
    {
        $preference = new Preference();

        $item = new Item();
        $item->title = $produto['titulo'];
        $item->quantity = $produto['quantidade'];
        $item->unit_price = $produto['preco'];

        $payer = new Payer();
        $payer->name = $pagador['nome'];
        $payer->email = $pagador['email'];

        $preference->items = [$item];
        $preference->payer = $payer;

        $preference->back_urls = [
            "success" => "https://www.seusite.com/sucesso",
            "failure" => "https://www.seusite.com/falha",
            "pending" => "https://www.seusite.com/pendente"
        ];
        $preference->auto_return = "approved";

        $preference->save();

        return $preference->init_point;
    }
}
