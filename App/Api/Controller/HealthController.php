<?php

namespace App\Api\Controller;

use App\Api\ApiController;
use FW\DB\Connection;

/**
 * Endpoints de diagnóstico da API (sem regra de negócio).
 * Servem para validar a fundação: roteamento, JSON, conexão e JWT.
 */
class HealthController extends ApiController
{
    /** GET /api/ping — público. Confirma que a API está no ar. */
    public function ping()
    {
        $this->json([
            'pong'      => true,
            'servico'   => 'API AmigoPet',
            'versao'    => '1.0.0',
            'timestamp' => date('c'),
        ]);
    }

    /** GET /api/health — público. Confirma a conectividade com o banco. */
    public function health()
    {
        $banco   = 'falha';
        $tabelas = null;
        $ok      = false;

        try {
            $conn = (new Connection())->getConn();
            $tabelas = (int) $conn
                ->query('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()')
                ->fetchColumn();
            $banco = 'conectado';
            $ok = true;
        } catch (\Throwable $e) {
            error_log('[API/health] ' . $e->getMessage());
        }

        $this->json([
            'status'    => $ok ? 'ok' : 'degradado',
            'banco'     => $banco,
            'tabelas'   => $tabelas,
            'timestamp' => date('c'),
        ], $ok ? 200 : 503);
    }

    /** GET /api/me — protegido. Ecoa o payload do JWT recebido. */
    public function me()
    {
        $usuario = $this->exigirAutenticacao();
        $this->json(['usuario' => $usuario]);
    }
}
