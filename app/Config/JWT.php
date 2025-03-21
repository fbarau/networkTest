<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class JWT extends BaseConfig
{
    public string $secretKey;
    public string $algorithm = "HS256";
    public int $expiration = 3600; // 1 hora

    public function __construct()
    {
        // $secretKeyPath = __DIR__ . "/app_protocol_key.txt";

        $secretKeyPath = dirname(__DIR__) . "\app_protocol_key.txt";

        $secret = trim(file_get_contents($secretKeyPath));

        if (!file_exists($secretKeyPath)) {
            die("Erro: Arquivo de chave secreta não encontrado.");
        }

        parent::__construct();
        $this->secretKey = getenv('JWT_SECRET') ?: $secret; // Chave padrão caso não esteja definida no .env
    }
}
