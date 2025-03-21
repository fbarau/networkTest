<?php

namespace App\Controllers;

use Firebase\JWT\JWT;

class AuthController extends BaseController
{
    public function generateToken()
    {
        $keyPath = dirname(__DIR__) . "\app_protocol_key.txt"; // Caminho da chave

        if (!file_exists($keyPath)) {
            return $this->response->setJSON(['error' => 'Arquivo de chave não encontrado!'])->setStatusCode(500);
        }

        $key = trim(file_get_contents($keyPath)); // Ler chave secreta

        // Dados do payload (informações do usuário)
        $payload = [
            'userId' => 1,
            'email'  => 'usuario@exemplo.com',
            'iat'    => time(), // Timestamp de criação
            'exp'    => time() + 3600 // Expira em 1 hora
        ];

        // Gerar token JWT
        $token = JWT::encode($payload, $key, 'HS256');

        return $this->response->setJSON(['token' => $token]);
    }
}
