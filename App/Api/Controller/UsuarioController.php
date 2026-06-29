<?php

namespace App\Api\Controller;

use App\Api\ApiController;
use App\Api\ApiException;
use App\Api\Repository\UsuarioRepository;

/**
 * Módulo de busca de usuários (seção C.13 da especificação).
 * Endpoint: GET /usuarios/busca?q={query} (🔒 autenticado).
 *
 * Retorna array puro de até 10 usuários. Sintaxe PHP 7.0.
 */
class UsuarioController extends ApiController
{
    /** GET /usuarios/busca?q={query} — 200 array · 422 · 🔒 JWT. */
    public function buscar()
    {
        $this->exigirAutenticacao();

        $q = $this->query('q');
        if ($q === null || mb_strlen(trim($q)) < 2) {
            throw new ApiException('Informe ao menos 2 caracteres para a busca.', 422);
        }

        $repo = new UsuarioRepository();
        $this->json($repo->buscar($q));
    }
}
