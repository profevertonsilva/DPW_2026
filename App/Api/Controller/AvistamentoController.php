<?php

namespace App\Api\Controller;

use App\Api\ApiController;
use App\Api\ApiException;
use App\Api\Repository\AvistamentoRepository;

/**
 * Módulo de Avistamentos (animal de rua) + Ranking de rastreadores (seção C.6).
 *
 * Reportar é capability de QUALQUER usuário logado (não existe mais "rastreador").
 * Mudança de status é restrita a ONG/admin. Status do app usam underscore.
 * Sintaxe PHP 7.0.
 */
class AvistamentoController extends ApiController
{
    const STATUS_VALIDOS = ['aguardando_acolhimento', 'em_acolhimento', 'resgatado', 'encerrado'];

    /** POST /api/avistamentos — qualquer logado. 201 · 422 sem localização. */
    public function criar()
    {
        $usuario = $this->exigirAutenticacao();
        $body    = $this->body();

        if (!$this->temLocalizacao($body)) {
            throw new ApiException('Informe a localização (GPS lat/lng ou endereço).', 422);
        }

        $repo = new AvistamentoRepository();
        $id   = $repo->criar((int) $usuario->sub, $body);
        $this->json($repo->buscarPorId($id), 201);
    }

    /** GET /api/avistamentos — query status?, especie?. 200 array. */
    public function listar()
    {
        $this->exigirAutenticacao();

        $filtros = [];
        foreach (['status', 'especie'] as $campo) {
            $val = $this->query($campo);
            if ($val !== null && trim($val) !== '') {
                $filtros[$campo] = trim($val);
            }
        }

        $repo = new AvistamentoRepository();
        $this->json($repo->listar($filtros));
    }

    /** GET /api/avistamentos/{id} — 200 · 404. */
    public function detalhe()
    {
        $this->exigirAutenticacao();
        $repo = new AvistamentoRepository();
        $av   = $repo->buscarPorId((int) $this->param('id'));
        if (!$av) {
            throw new ApiException('Avistamento não encontrado.', 404);
        }
        $this->json($av);
    }

    /** PATCH /api/avistamentos/{id}/status — ong/admin. 200 · 403 · 404 · 422. */
    public function atualizarStatus()
    {
        $usuario = $this->exigirAutenticacao();
        $tipo    = isset($usuario->tipo_usuario) ? $usuario->tipo_usuario : null;
        if ($tipo !== 'ong' && $tipo !== 'administrador') {
            throw new ApiException('Apenas ONGs ou administradores podem alterar o status.', 403);
        }

        $repo = new AvistamentoRepository();
        $id   = (int) $this->param('id');
        if (!$repo->existe($id)) {
            throw new ApiException('Avistamento não encontrado.', 404);
        }

        $body = $this->body();
        $this->exigirCampos($body, ['status']);
        if (!in_array($body['status'], self::STATUS_VALIDOS, true)) {
            throw new ApiException('Status inválido. Use: ' . implode(', ', self::STATUS_VALIDOS) . '.', 422);
        }

        $this->json($repo->atualizarStatus($id, $body['status']));
    }

    /** GET /api/ranking/rastreadores — 200 array. */
    public function ranking()
    {
        $this->exigirAutenticacao();
        $repo = new AvistamentoRepository();
        $this->json($repo->ranking());
    }

    // ----------------------------------------------------------------- helpers

    /** Localização = GPS (lat+lng) OU endereço (endereco_texto / cidade). */
    private function temLocalizacao(array $body): bool
    {
        $temGps = isset($body['lat']) && $body['lat'] !== '' && isset($body['lng']) && $body['lng'] !== '';
        $temEnd = (isset($body['endereco_texto']) && trim((string) $body['endereco_texto']) !== '')
            || (isset($body['local_cidade']) && trim((string) $body['local_cidade']) !== '');
        return $temGps || $temEnd;
    }
}
