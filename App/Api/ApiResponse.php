<?php

namespace App\Api;

/**
 * Centraliza a saída HTTP da API (JSON e CORS).
 * Sempre encerra a requisição após escrever a resposta.
 * Sintaxe compatível com PHP 7.0 (piso declarado no composer.json).
 */
class ApiResponse
{
    /**
     * Cabeçalhos CORS. Responde de imediato a requisições OPTIONS (preflight).
     */
    public static function cors()
    {
        if (!headers_sent()) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            header('Access-Control-Max-Age: 86400');
        }

        $metodo = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
        if ($metodo === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }

    /**
     * Escreve um corpo JSON com o status informado e encerra a requisição.
     *
     * @param mixed $dados
     * @param int   $status
     */
    public static function json($dados, $status = 200)
    {
        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: application/json; charset=utf-8');
        }

        echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
