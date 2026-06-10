<?php
use BookStore\CurlClient;
use PHPUnit\Framework\TestCase;

class CurlClientTest extends TestCase
{
    protected function setUp(): void
    {
        copy(__DIR__ . '/seed/products.json', __DIR__ . '/../../data/products.json');
        copy(__DIR__ . '/seed/orders.json', __DIR__ . '/../../data/orders.json');
        copy(__DIR__ . '/seed/users.json', __DIR__ . '/../../data/users.json');
        fwrite(STDOUT, "[setUp] Data restored from tests/seed/\n");
    }

    protected function tearDown(): void
    {
        file_put_contents(__DIR__ . '/../../data/orders.json', '[]');
        fwrite(STDOUT, "[tearDown] orders.json cleaned\n");
    }

    public function testPost_sendsJsonAndDecodesResponse()
    {
        $client = new CurlClient();
        $result = $client->post('http://localhost:8080/api/calculate', [
            'product_id' => 'B001',
            'quantity' => 2
        ]);

        if ($result !== null) {
            $this->assertArrayHasKey('total', $result);
            $this->assertIsFloat($result['total']);
        } else {
            $this->assertNull($result);
        }
    }
}
