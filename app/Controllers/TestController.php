<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class TestController extends ResourceController
{
    public function testando()
    {
        // $secretKeyPath = "C:\\xampp\\htdocs\\networkTest\\app\\app_protocol_key.txt";
        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Método não permitido'
            ])->setStatusCode(405);
        }

        // Verifica se o CSRF está presente
        $token = $this->request->header('csrf_cookie_name')->getValue();

        if (!$token) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'CSRF Token ausente'
            ])->setStatusCode(403);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Login efetuado com sucesso'
        ]);
    }
}
