<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\JWT as JWTConfig;

function validateJWT($token)
{
    $keyPath = __DIR__ . '/../app_protocol_key.txt';

    if (!file_exists($keyPath)) {
        return false;
    }

    $key = trim(file_get_contents($keyPath));

    try {
        return JWT::decode($token, new Key($key, 'HS256'));
    } catch (\Exception $e) {
        return false;
    }
}

if (!function_exists('generateJWT')) {
    function generateJWT($userData)
    {
        $config = new JWTConfig();
        $payload = [
            'iat' => time(), // Tempo em que o token foi gerado
            'exp' => time() + $config->expiration, // Expiração do token
            'data' => $userData // Dados do usuário
        ];

        return JWT::encode($payload, $config->secretKey, $config->algorithm);
    }
}

if (!function_exists('validateJWT')) {
    function validateJWT($token)
    {
        $config = new JWTConfig();

        try {
            return JWT::decode($token, new Key($config->secretKey, $config->algorithm));
        } catch (Exception $e) {
            return null; // Token inválido
        }
    }
}
