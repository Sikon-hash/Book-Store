<?php
namespace BookStore;

class CurlClient implements HttpClientInterface
{
    public function post(string $url, array $data): ?array
    {
        $payload = json_encode($data);
        $response = $this->doCurl($url, $payload);
        return $response !== false ? json_decode($response, true) : null;
    }

    protected function doCurl(string $url, string $payload): string|false
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
