<?php
namespace App\Middleware;

class CheckRequest
{
    private array $http;

    public function __construct()
    {
        $this->http = [
            'method'  => htmlentities($_SERVER['REQUEST_METHOD'] ?? 'GET', ENT_QUOTES, 'UTF-8'),
            'uri'     => htmlentities($_SERVER['REQUEST_URI'] ?? '/', ENT_QUOTES, 'UTF-8'),
            'host'    => htmlentities($_SERVER['HTTP_HOST'] ?? '', ENT_QUOTES, 'UTF-8'),
            'scheme'  => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http',
            'get'     => $this->sanitize($_GET),
            'post'    => $this->sanitize($_POST),
            'cookies' => $this->sanitize($_COOKIE),
        ];
    }

    private function sanitize(array $data): array
    {
        return array_map(
            fn($v) => is_array($v) ? $this->sanitize($v) : htmlentities((string)$v, ENT_QUOTES, 'UTF-8'),
            $data
        );
    }

    public function getHTTP(): array
    {
        return $this->http;
    }
}