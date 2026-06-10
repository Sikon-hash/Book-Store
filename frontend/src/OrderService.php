<?php
namespace BookStore;

class OrderService
{
    private HttpClientInterface $httpClient;

    public function __construct(?HttpClientInterface $httpClient = null)
    {
        $this->httpClient = $httpClient ?? new CurlClient();
    }

    public function sendOrderRequest(string $productId, int $quantity): ?array
    {
        return $this->httpClient->post(
            'http://localhost:8080/api/calculate',
            [
                'product_id' => $productId,
                'quantity' => $quantity
            ]
        );
    }
}
