<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

helper('jwt_helper');

class JWTAuth implements FilterInterface
{
    function validateJWT($token)
    {
        $keyPath = dirname(__DIR__) . "\app_protocol_key.txt"; // Caminho correto da chave

        if (!file_exists($keyPath)) {
            return false;
        }

        $key = trim(file_get_contents($keyPath));

        try {
            return \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($key, 'HS256'));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        error_log("Cabeçalho recebido: " . $authHeader);

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return \Config\Services::response()
                ->setStatusCode(401)
                ->setJSON(['message' => 'Token não informado ou inválido.']);
        }

        $token = $matches[1];
        $decoded = validateJWT($token);

        /* if (!$decoded) {
            return \Config\Services::response()
                ->setStatusCode(401)
                ->setJSON(['message' => 'Token inválido ou expirado.']);
        } */

        return null; // Permite o acesso
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada a fazer aqui
    }
}
