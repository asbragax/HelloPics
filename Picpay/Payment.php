<?php

namespace PicPay;

class Payment
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function create(array $payload): array
    {
        return $this->client->post('/payments', $payload);
    }

    public function status(string $referenceId): array
    {
        return $this->client->get("/payments/{$referenceId}/status");
    }

    public function cancel(string $referenceId): array
    {
        return $this->client->post("/payments/{$referenceId}/cancellations", []);
    }
}
