<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class NetworkController extends ResourceController
{
    protected $format  = 'json';

    public function test()
    {
        return $this->response->setJSON([
            'csrf_token' => csrf_token(),
            'csrf_hash' => csrf_hash()
        ]);
        // return $this->respond(['message' => 'API funcionando no XAMPP!'], 200);
    }

    /**
     * Define um IP para os testes (ping, jitter, traceroute)
     */

    public function getStoredIps()
    {
        $ips = session()->get('ips');

        if (!$ips) {
            return $this->fail('Nenhum IP armazenado.', 404);
        }

        return $this->respond(['stored_ips' => $ips], 200);
    }

    public function setIp()
    {
        // Obtém os dados do corpo da requisição JSON
        $requestData = $this->request->getJSON(true);

        // Verifica se a chave 'ips' foi enviada e se é um array
        if (!isset($requestData['ips']) || !is_array($requestData['ips'])) {
            return $this->fail('Lista de IPs não informada ou formato inválido.', 400);
        }

        // Valida e filtra os IPs
        $validIps = [];
        foreach ($requestData['ips'] as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                $validIps[] = $ip;
            }
        }

        // Se nenhum IP for válido, retorna erro
        if (empty($validIps)) {
            return $this->fail('Nenhum IP válido informado.', 400);
        }

        // Armazena os IPs na sessão (ou poderia salvar em um banco de dados)
        session()->set('ips', $validIps);

        return $this->respond(['message' => 'IPs armazenados com sucesso!', 'ips' => $validIps], 200);
    }


    /**
     * Testa a latência (ping) para um IP ou domínio
     */

    public function ping()
    {
        // Obter o JSON da requisição
        $json = $this->request->getJSON(true);

        // Verifica se a chave "ips" existe e se é um array
        if (!isset($json['ips']) || !is_array($json['ips']) || empty($json['ips'])) {
            return $this->fail('Lista de IPs não informada ou formato inválido.');
        }

        $sistema = strtoupper(PHP_OS);
        $resultados = [];

        foreach ($json['ips'] as $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                $resultados[$ip] = "IP inválido";
                continue;
            }

            // Define o comando conforme o sistema operacional
            if (strpos($sistema, 'WIN') !== false) {
                $output = shell_exec("ping -n 4 $ip");
            } else {
                $output = shell_exec("ping -c 4 $ip");
            }

            // Verifica se houve erro na execução
            if (!$output) {
                $resultados[$ip] = "Falha ao executar o ping";
            } else {
                $output = trim($output);
                $output = str_replace("\n", " ", $output);
                $output = mb_convert_encoding($output, 'UTF-8', 'ISO-8859-1');
                $resultados[$ip] = $output;
            }
        }

        return $this->respond(['results' => $resultados], 200);
    }


    /* public function ping($ip = null)
    {
        $ip = session()->get('ip');
        if (!$ip) {
            return $this->fail('IP não informado.');
        }

        // Executa o comando ping (4 pacotes)
        // $output = shell_exec("ping -c 4 $ip"); -- comando servidor Linux
        $sistema = strtoupper(PHP_OS);

        // Verifique o sistema operacional
        if (strpos($sistema, 'WIN') !== false) {
            $output = shell_exec("ping -n 4 $ip");
        } else {
            $output = shell_exec("ping -c 4 $ip");
        }

        if (!$output) {
            return $this->failServerError('Falha ao executar o ping');
        }

        // Verifique a saída
        error_log("Saída do comando ping: $output");

        // Limpeza da saída para formato de JSON
        $output = trim($output); // Remover espaços em branco extras
        // $output = utf8_encode($output); // Codifica a saída para UTF-8, se necessário

        // Substitui as quebras de linha (\n) por outro caractere (exemplo: espaço)
        $output = str_replace("\n", " ", $output);

        // Codifica para UTF-8
        $output = utf8_encode($output);

        // Retorna o resultado
        return $this->respond(['result' => $output], 200);
    } */

    /**
     * Retorna os hops do traceroute para um IP ou domínio
     */
    public function traceroute($ip = null)
    {
        $ip = session()->get('ips');

        // Obter o JSON da requisição
        $json = $this->request->getJSON(true);

        // Verifica se a chave "ips" existe e se é um array
        if (!isset($json['ips']) || !is_array($json['ips']) || empty($json['ips'])) {
            return $this->fail('Lista de IPs não informada ou formato inválido.');
        }

        set_time_limit(120);  // Aumenta o limite para 120 segundos
        // Executa o traceroute
        // $output = shell_exec("traceroute $ip"); -- comando servidor Linux
        $sistema = strtoupper(PHP_OS);

        $start_time = microtime(true);

        foreach ($json['ips'] as $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                $resultados[$ip] = "IP inválido";
                continue;
            }

            // Define o comando conforme o sistema operacional
            if (strpos($sistema, 'WIN') !== false) {
                $output = shell_exec("tracert -h 30 $ip");
            } else {
                $output = shell_exec("traceroute -m 30 $ip");
            }

            // Verifica se houve erro na execução
            if (!$output) {
                $resultados[$ip] = "Falha ao executar o ping";
            } else {
                $output = trim($output);
                $output = str_replace("\n", " ", $output);
                $output = mb_convert_encoding($output, 'UTF-8', 'ISO-8859-1');
                $resultados[$ip] = $output;
            }
        }

        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;

        return $this->respond(['results' => $resultados], 200);

        /* if (!$ip) {
            return $this->fail('IP não informado.');
        }
        set_time_limit(120);  // Aumenta o limite para 120 segundos
        // Executa o traceroute
        // $output = shell_exec("traceroute $ip"); -- comando servidor Linux
        $sistema = strtoupper(PHP_OS);

        $start_time = microtime(true);

        if (strpos($sistema, 'WIN') !== false) {
            $output = shell_exec("tracert -h 30 $ip");
        } else {
            $output = shell_exec("traceroute -m 30 $ip");
        }

        if (!$output) {
            return $this->failServerError('Falha ao executar o tracert');
        }

        // Limpeza da saída para formato de JSON
        $output = trim($output); // Remover espaços em branco extras

        // Substitui as quebras de linha (\n) por outro caractere (exemplo: espaço)
        $output = str_replace("\n", " ", $output);

        // Codifica para UTF-8
        $output = mb_convert_encoding($output, 'UTF-8', 'ISO-8859-1');

        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;

        return $this->respond(['result' => nl2br($output)], 200); */
    }

    /**
     * Mede jitter e perda de pacotes usando ping
     */
    public function jitter($ip = null)
    {
        // Obter o JSON da requisição
        $json = $this->request->getJSON(true);

        // Verifica se a chave "ips" existe e se é um array
        if (!isset($json['ips']) || !is_array($json['ips']) || empty($json['ips'])) {
            return $this->fail('Lista de IPs não informada ou formato inválido.');
        }

        $sistema = strtoupper(PHP_OS);
        $resultados = [];

        foreach ($json['ips'] as $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                $resultados[$ip] = "IP inválido";
                continue;
            }

            // Define o comando conforme o sistema operacional
            if (strpos($sistema, 'WIN') !== false) {
                $output = shell_exec("ping -n 10 $ip | findstr \"Média\"");
            } else {
                $output = shell_exec("ping -c 10 $ip | tail -3");
            }

            // Verifica se houve erro na execução
            if (!$output) {
                $resultados[$ip] = "Falha ao executar o ping";
            } else {
                $output = trim($output);
                $output = str_replace("\n", " ", $output);
                $output = mb_convert_encoding($output, 'UTF-8', 'ISO-8859-1');
                $resultados[$ip] = $output;
            }
        }

        return $this->respond(['results' => $resultados], 200);

        /* $ip = session()->get('ip');

        if (!$ip) {
            return $this->fail('IP não informado.');
        }

        // Detecta se está rodando no Windows
        $sistema = strtoupper(PHP_OS);

        if (strpos($sistema, 'WIN') !== false) {
            // Windows: Usa ping -n e filtra a média
            $output = shell_exec("ping -n 10 $ip | findstr \"Média\"");
        } else {
            // Linux: Usa ping -c e tail
            $output = shell_exec("ping -c 10 $ip | tail -3");
        }

        if (!$output) {
            return $this->failServerError('Falha na execução');
        }

        // Limpeza da saída para formato de JSON
        $output = trim($output); // Remover espaços em branco extras

        // Substitui as quebras de linha (\n) por outro caractere (exemplo: espaço)
        $output = str_replace("\n", " ", $output);

        // Codifica para UTF-8
        $output = mb_convert_encoding($output, 'UTF-8', 'ISO-8859-1');

        return $this->respond(['result' => nl2br($output)], 200); */
    }
}
