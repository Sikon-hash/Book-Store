<?php
namespace BookStore;

interface HttpClientInterface
{
    public function post(string $url, array $data): ?array;
}
