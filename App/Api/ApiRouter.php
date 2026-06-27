<?php

namespace App\Api;

/**
 * Roteador dedicado da API REST. Independente do roteamento web (tabela `routes`
 * + FW\Init\Boostrap): as rotas de API são declaradas em código (App/Api/routes.php),
 * mantendo a camada JSON/JWT isolada da camada web (sessão/HTML).
 *
 * Suporta parâmetros de caminho no formato {nome} (ex.: /animais/{id}).
 * Sintaxe compatível com PHP 7.0 (piso declarado no composer.json).
 */
class ApiRouter
{
    /** @var array<int,array> Lista de rotas registradas. */
    private $rotas = [];

    public function adicionar($metodo, $caminho, $controller, $acao, $protegida = false)
    {
        $this->rotas[] = [
            'metodo'     => strtoupper($metodo),
            'caminho'    => $caminho,
            'controller' => $controller,
            'acao'       => $acao,
            'protegida'  => $protegida,
        ];
    }

    /**
     * Resolve a requisição atual: aplica CORS, casa método + caminho,
     * executa o controller e devolve JSON. Sempre encerra a requisição.
     */
    public function despachar()
    {
        ApiResponse::cors();

        $metodo  = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $caminho = $this->caminhoAtual();

        $caminhoExisteOutroMetodo = false;

        foreach ($this->rotas as $rota) {
            $params = [];
            if (!$this->casa($rota['caminho'], $caminho, $params)) {
                continue;
            }
            if ($rota['metodo'] !== $metodo) {
                $caminhoExisteOutroMetodo = true;
                continue;
            }
            $this->executar($rota, $params);
            return;
        }

        if ($caminhoExisteOutroMetodo) {
            ApiResponse::json(['erro' => 'Método não permitido para este recurso.'], 405);
        }

        ApiResponse::json(['erro' => 'Recurso não encontrado.'], 404);
    }

    /** Caminho da requisição sem o prefixo /api e sem query string. */
    private function caminhoAtual(): string
    {
        $uri  = parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH);
        $uri  = $uri === null ? '/' : $uri;
        $semApi = preg_replace('#^/api#', '', $uri);
        return '/' . trim($semApi, '/');
    }

    /**
     * Casa o padrão da rota (com {param}) contra o caminho, extraindo os params.
     */
    private function casa($padrao, $caminho, array &$params): bool
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $padrao);
        $regex = '#^' . ($regex === '/' ? '/' : rtrim($regex, '/')) . '/?$#';

        if (!preg_match($regex, $caminho, $m)) {
            return false;
        }

        foreach ($m as $chave => $valor) {
            if (\is_string($chave)) {
                $params[$chave] = $valor;
            }
        }
        return true;
    }

    /**
     * Instancia o controller, aplica autenticação (se protegida) e chama a ação,
     * convertendo qualquer erro no padrão JSON da API.
     */
    private function executar(array $rota, array $params)
    {
        try {
            $classe = 'App\\Api\\Controller\\' . $rota['controller'];

            if (!class_exists($classe)) {
                throw new ApiException('Controller da API não encontrado: ' . $rota['controller'], 500);
            }

            $controller = new $classe($params);

            if (!method_exists($controller, $rota['acao'])) {
                throw new ApiException('Ação não encontrada: ' . $rota['acao'], 500);
            }

            if ($rota['protegida']) {
                $controller->exigirAutenticacao();
            }

            $controller->{$rota['acao']}();
        } catch (ApiException $e) {
            ApiResponse::json(['erro' => $e->getMessage()], $e->status());
        } catch (\Throwable $e) {
            // Detalhe técnico fica no log do servidor; cliente recebe mensagem genérica.
            error_log('[API] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
            ApiResponse::json(['erro' => 'Erro interno no servidor.'], 500);
        }
    }
}
