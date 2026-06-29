<?php

namespace App\Api\Controller;

use App\Api\ApiController;
use App\Api\ApiException;
use App\Api\Repository\SaudeRepository;

/**
 * Módulo de Saúde do Animal (seção C.5).
 * Vacinas/procedimentos (append-only), saúde (upsert), carteira (QR/PDF stub),
 * auditoria e atendimentos do veterinário. Todos os endpoints exigem JWT.
 * Sintaxe PHP 7.0.
 */
class SaudeController extends ApiController
{
    const TIPOS_PROCEDIMENTO = ['consulta', 'cirurgia', 'exame', 'castracao', 'outro'];

    /** GET /api/animais/{id}/vacinas — 200 array. */
    public function vacinas()
    {
        $this->exigirAutenticacao();
        $repo = new SaudeRepository();
        $id   = $this->animalIdValido($repo);
        $this->json($repo->listarVacinas($id));
    }

    /** POST /api/animais/{id}/vacinas — vet/ong. 201 (append-only). */
    public function adicionarVacina()
    {
        $usuario = $this->exigirAutenticacao();
        $this->exigirVetOuOng($usuario);

        $repo = new SaudeRepository();
        $id   = $this->animalIdValido($repo);

        $body = $this->body();
        $this->exigirCampos($body, ['nome', 'data_aplicacao']);

        $autorResolvido = $repo->resolverAutor((int) $usuario->sub, $usuario->tipo_usuario);
        $novoId = $repo->adicionarVacina($id, $body, $autorResolvido);

        $vacinas = $repo->listarVacinas($id);
        $this->json($this->acharPorId($vacinas, $novoId), 201);
    }

    /** GET /api/animais/{id}/procedimentos — 200 array. */
    public function procedimentos()
    {
        $this->exigirAutenticacao();
        $repo = new SaudeRepository();
        $id   = $this->animalIdValido($repo);
        $this->json($repo->listarProcedimentos($id));
    }

    /** POST /api/animais/{id}/procedimentos — vet/ong. 201 (append-only). */
    public function adicionarProcedimento()
    {
        $usuario = $this->exigirAutenticacao();
        $this->exigirVetOuOng($usuario);

        $repo = new SaudeRepository();
        $id   = $this->animalIdValido($repo);

        $body = $this->body();
        $this->exigirCampos($body, ['nome', 'tipo', 'data']);
        if (!in_array($body['tipo'], self::TIPOS_PROCEDIMENTO, true)) {
            throw new ApiException('tipo inválido. Use: ' . implode(', ', self::TIPOS_PROCEDIMENTO) . '.', 422);
        }

        $autorResolvido = $repo->resolverAutor((int) $usuario->sub, $usuario->tipo_usuario);
        $novoId = $repo->adicionarProcedimento($id, $body, $autorResolvido);

        $procs = $repo->listarProcedimentos($id);
        $this->json($this->acharPorId($procs, $novoId), 201);
    }

    /** GET /api/animais/{id}/saude — 200. */
    public function saude()
    {
        $this->exigirAutenticacao();
        $repo = new SaudeRepository();
        $id   = $this->animalIdValido($repo);
        $this->json($repo->saude($id));
    }

    /** PUT /api/animais/{id}/saude — vet/ong. upsert. 200. */
    public function atualizarSaude()
    {
        $usuario = $this->exigirAutenticacao();
        $this->exigirVetOuOng($usuario);

        $repo = new SaudeRepository();
        $id   = $this->animalIdValido($repo);

        $this->json($repo->salvarSaude($id, $this->body()));
    }

    /** GET /api/animais/{id}/carteira — 200. Público (QR code). */
    public function carteira()
    {
        $repo     = new SaudeRepository();
        $id       = (int) $this->param('id');
        $carteira = $repo->carteira($id);
        if (!$carteira) {
            throw new ApiException('Animal não encontrado.', 404);
        }
        $this->json($carteira);
    }

    /** GET /api/vet/atendimentos — vet. 200 array de animais atendidos. */
    public function atendimentos()
    {
        $usuario = $this->exigirAutenticacao();
        if (!isset($usuario->tipo_usuario) || $usuario->tipo_usuario !== 'veterinario') {
            throw new ApiException('Apenas veterinários têm atendimentos.', 403);
        }
        $repo = new SaudeRepository();
        $this->json($repo->atendimentosDoVet((int) $usuario->sub));
    }

    // ----------------------------------------------------------------- helpers

    /** Valida o {id} de animal na rota; 404 se não existir. Retorna o id (int). */
    private function animalIdValido(SaudeRepository $repo)
    {
        $id = (int) $this->param('id');
        if (!$repo->animalExiste($id)) {
            throw new ApiException('Animal não encontrado.', 404);
        }
        return $id;
    }

    /** Exige vet ou ong (quem registra saúde). Retorna o tipo. */
    private function exigirVetOuOng($usuario)
    {
        $tipo = isset($usuario->tipo_usuario) ? $usuario->tipo_usuario : null;
        if ($tipo !== 'veterinario' && $tipo !== 'ong') {
            throw new ApiException('Apenas veterinários ou ONGs podem registrar dados de saúde.', 403);
        }
        return $tipo;
    }

    private function acharPorId(array $itens, $id)
    {
        foreach ($itens as $item) {
            if ((int) $item['id'] === (int) $id) {
                return $item;
            }
        }
        return null;
    }
}
