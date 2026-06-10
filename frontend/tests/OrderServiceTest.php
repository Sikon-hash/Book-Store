<?php
use BookStore\HttpClientInterface;
use BookStore\OrderService;
use PHPUnit\Framework\TestCase;

class OrderServiceTest extends TestCase
{
    public function testSendOrderRequest_passesCorrectProductIdAndQuantity()
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('post')
            ->with(
                'http://localhost:8080/api/calculate',
                $this->callback(function ($data) {
                    return isset($data['product_id'])
                        && $data['product_id'] === 'B001'
                        && isset($data['quantity'])
                        && $data['quantity'] === 3;
                })
            )
            ->willReturn(['total' => 300000]);

        $orderService = new OrderService($httpClient);
        $result = $orderService->sendOrderRequest('B001', 3);

        $this->assertEquals(['total' => 300000], $result);
    }

    public function testSendOrderRequest_payloadHasRequiredFields()
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('post')
            ->with(
                $this->anything(),
                $this->callback(function ($data) {
                    return is_array($data)
                        && array_key_exists('product_id', $data)
                        && array_key_exists('quantity', $data)
                        && is_string($data['product_id'])
                        && is_int($data['quantity']);
                })
            );

        $orderService = new OrderService($httpClient);
        $orderService->sendOrderRequest('B002', 5);
    }

    public function testSendOrderRequest_usesCorrectEndpoint()
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('post')
            ->with(
                'http://localhost:8080/api/calculate',
                $this->anything()
            );

        $orderService = new OrderService($httpClient);
        $orderService->sendOrderRequest('B003', 1);
    }

    public function testSendOrderRequest_returnsNullOnFailure()
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('post')
            ->willReturn(null);

        $orderService = new OrderService($httpClient);
        $result = $orderService->sendOrderRequest('B001', 10);

        $this->assertNull($result);
    }
}
