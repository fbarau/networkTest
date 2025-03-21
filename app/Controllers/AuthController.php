<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;

class AuthController extends ResourceController
{
    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Simulação de usuário válido
        if ($username === 'admin' && $password === '123456') {
            $secretKeyPath = __DIR__ . "/app_protocol_key.txt";

            if (!file_exists($secretKeyPath)) {
                return $this->failServerError('Chave secreta não encontrada.');
            }

            $secretKey = trim(file_get_contents($secretKeyPath));

            $payload = [
                'iss' => 'localhost:8080',
                'iat' => time(),
                'exp' => time() + (60 * 60), // 1 hora
                'sub' => $username
            ];

            $token = JWT::encode($payload, $secretKey, 'HS256');

            return $this->respond(['token' => $token], 200);
        }

        return $this->failUnauthorized('Usuário ou senha inválidos.');

        // $secretKeyPath = __DIR__ . "/app_protocol_key.txt";
        // $secretKey = trim(file_get_contents($secretKeyPath));

        // $json = $this->request->getJSON();

        // if (!isset($json->email) || !isset($json->password)) {
        //     return $this->fail('Email e senha são obrigatórios.', 400);
        // }

        // // 🔍 Ler os usuários do arquivo JSON
        // $usersJson = file_get_contents(APPPATH . 'Config/users.json');
        // $users = json_decode($usersJson, true);

        // if (!$users) {
        //     return $this->failServerError('Erro ao carregar os usuários.');
        // }

        // // 🔍 Verificar se o usuário existe e a senha está correta
        // foreach ($users as $user) {
        //     if ($user['email'] === $json->email && password_verify($json->password, $user['password'])) {
        //         // 🏷️ Gerar o token JWT
        //         $payload = [
        //             'id' => $user['id'] ?? null,
        //             'email' => $user['email'],
        //             'iat' => time(),
        //             'exp' => time() + 3600 // Token válido por 1 hora
        //         ];

        //         $token = JWT::encode($payload, $secretKey, 'HS256');

        //         return $this->respond(['token' => $token], 200);
        //     }
        // }

        // return $this->failUnauthorized("Usuário ou senha inválidos.");
    }
}
