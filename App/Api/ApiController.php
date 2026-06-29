<?php

namespace App\Api;

/**
 * Controller base da API. Diferente de FW\Controller\Action, NÃO usa sessão
 * nem views: trabalha apenas com JSON e autenticação por JWT (stateless).
 * Todos os controllers de API devem estender esta classe.
 *
 * Escrito em sintaxe compatível com PHP 7.0 (piso declarado no composer.json).
 */
abstract class ApiController
{
    /** @var array Parâmetros de rota (ex.: ['id' => '5']). */
    protected $params;

    /** @var object|null Payload do JWT já validado (cache por requisição). */
    private $usuario = null;

    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /** Parâmetro de rota (ex.: {id}). */
    protected function param($nome, $default = null)
    {
        return isset($this->params[$nome]) ? $this->params[$nome] : $default;
    }

    /** Parâmetro de query string (?chave=valor). */
    protected function query($nome, $default = null)
    {
        return isset($_GET[$nome]) ? $_GET[$nome] : $default;
    }

    /**
     * Corpo da requisição decodificado de JSON para array associativo.
     * Lança ApiException 400 se o corpo não for JSON válido.
     */
    protected function body(): array
    {
        $bruto = file_get_contents('php://input');
        if ($bruto === '' || $bruto === false) {
            return [];
        }

        $dados = json_decode($bruto, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException('Corpo da requisição não é um JSON válido.', 400);
        }

        return \is_array($dados) ? $dados : [];
    }

    /**
     * Exige um JWT válido no header Authorization: Bearer {token}.
     * Retorna o payload do token (sub, email, tipo_usuario, status, ...).
     * Pública para que o ApiRouter possa aplicá-la em rotas protegidas.
     */
    public function exigirAutenticacao()
    {
        if ($this->usuario !== null) {
            return $this->usuario;
        }

        $header = $this->headerAutorizacao();
        if (!$header || !preg_match('/Bearer\s+(\S+)/i', $header, $m)) {
            throw new ApiException('Token de autenticação ausente.', 401);
        }

        try {
            $this->usuario = Jwt::validar(trim($m[1]));
        } catch (ApiException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new ApiException('Token inválido ou expirado.', 401);
        }

        return $this->usuario;
    }

    /** Resposta JSON de sucesso/erro (encerra a requisição). */
    protected function json($dados, $status = 200)
    {
        ApiResponse::json($dados, $status);
    }

    /**
     * Garante a presença (não-vazia) dos campos informados no corpo.
     * Lança ApiException 422 listando os que faltam.
     */
    protected function exigirCampos(array $body, array $campos)
    {
        $faltando = [];
        foreach ($campos as $campo) {
            $valor = isset($body[$campo]) ? $body[$campo] : null;
            if ($valor === null || (\is_string($valor) && trim($valor) === '')) {
                $faltando[] = $campo;
            }
        }

        if ($faltando) {
            throw new ApiException(
                'Campos obrigatórios faltando: ' . implode(', ', $faltando) . '.',
                422
            );
        }
    }

    /** IP do cliente (considera proxy via X-Forwarded-For). */
    protected function ip(): string
    {
        $encaminhado = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
        if ($encaminhado !== '') {
            return trim(explode(',', $encaminhado)[0]);
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }

    /**
     * Lê o header Authorization de forma robusta entre PHP embutido e Apache.
     */
    private function headerAutorizacao()
    {
        $header = null;
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $header = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        if (!$header && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers['Authorization'])) {
                $header = $headers['Authorization'];
            } elseif (isset($headers['authorization'])) {
                $header = $headers['authorization'];
            }
        }

        return $header;
    }
}
