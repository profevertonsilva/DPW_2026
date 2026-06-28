<?php

namespace App\Api\Controller;

use App\Api\ApiController;
use App\Api\ApiException;
use App\Api\Repository\SolicitacaoRepository;

/**
 * Módulo de Solicitações de Adoção + Termo + Avaliação (seções C.4 e C.7).
 *
 * Status canônicos (acento exato): Pendente, Em Análise, Aprovado, Concluído, Recusado.
 * Listas retornam array puro; detalhes, objeto puro. Sintaxe PHP 7.0.
 */
class SolicitacaoController extends ApiController
{
    const STATUS_VALIDOS = ['Pendente', 'Em Análise', 'Aprovado', 'Concluído', 'Recusado'];

    const TIPOS_MORADIA = ['casa_com_quintal', 'casa_sem_quintal', 'apartamento', 'outro'];
    const RESULTADOS    = ['aprovado', 'reprovado', 'pendente_informacoes'];

    /** GET /api/solicitacoes/minhas — protegido (adotante). 200 array. */
    public function minhas()
    {
        $usuario = $this->exigirAutenticacao();
        $repo    = new SolicitacaoRepository();
        $adotanteId = $this->exigirAdotante($usuario, $repo);

        $this->json($repo->listarDoAdotante($adotanteId));
    }

    /** GET /api/solicitacoes/recebidas — protegido (ong). 200 array. */
    public function recebidas()
    {
        $usuario = $this->exigirAutenticacao();
        $repo    = new SolicitacaoRepository();
        $ongId   = $this->exigirOng($usuario, $repo);

        $this->json($repo->listarRecebidasDaOng($ongId));
    }

    /** POST /api/solicitacoes — protegido (adotante). 201 · 400 · 422. */
    public function criar()
    {
        $usuario = $this->exigirAutenticacao();
        $repo    = new SolicitacaoRepository();
        $adotanteId = $this->exigirAdotante($usuario, $repo);

        $body = $this->body();
        $this->exigirCampos($body, ['fk_animal_id', 'motivo']);

        if (empty($body['aceite_termo'])) {
            throw new ApiException('É necessário aceitar o termo para solicitar a adoção.', 422);
        }

        $animalId = (int) $body['fk_animal_id'];

        $faltando = $repo->perfilAdotanteIncompleto($adotanteId);
        if ($faltando) {
            throw new ApiException('Complete seu perfil para adotar. Campos pendentes: ' . implode(', ', $faltando) . '.', 422);
        }

        if (!$repo->animalDisponivel($animalId)) {
            throw new ApiException('Animal indisponível para adoção.', 422);
        }

        if ($repo->existeSolicitacaoAtiva($adotanteId, $animalId)) {
            throw new ApiException('Você já possui uma solicitação ativa para este animal.', 400);
        }

        $id = $repo->criar(
            $adotanteId,
            $animalId,
            trim($body['motivo']),
            true,
            isset($body['timestamp_aceite']) ? $body['timestamp_aceite'] : null
        );

        $this->json($repo->buscarPorId($id), 201);
    }

    /** GET /api/solicitacoes/{id} — protegido. 200 · 403 · 404. */
    public function detalhe()
    {
        $usuario = $this->exigirAutenticacao();
        $repo    = new SolicitacaoRepository();
        $id      = (int) $this->param('id');

        $linha = $repo->buscarLinha($id);
        if (!$linha) {
            throw new ApiException('Solicitação não encontrada.', 404);
        }
        $ehOng = $this->garantirAcesso($usuario, $repo, $id, $linha);

        $this->json($repo->buscarPorId($id, $ehOng));
    }

    /** PATCH /api/solicitacoes/{id}/status — protegido (ong dona). 200 · 403 · 404 · 422. */
    public function atualizarStatus()
    {
        $usuario = $this->exigirAutenticacao();
        $repo    = new SolicitacaoRepository();
        $id      = (int) $this->param('id');

        $linha = $repo->buscarLinha($id);
        if (!$linha) {
            throw new ApiException('Solicitação não encontrada.', 404);
        }
        $this->exigirOngDona($usuario, $repo, $id);

        $body = $this->body();
        $this->exigirCampos($body, ['status']);
        $status = $body['status'];

        if (!in_array($status, self::STATUS_VALIDOS, true)) {
            throw new ApiException('Status inválido. Use: ' . implode(', ', self::STATUS_VALIDOS) . '.', 422);
        }
        if ($status === 'Recusado' && empty($body['motivo_recusa'])) {
            throw new ApiException('Informe o motivo da recusa.', 422);
        }

        $motivo = isset($body['motivo_recusa']) ? trim($body['motivo_recusa']) : null;
        $this->json($repo->atualizarStatus($id, $status, $motivo));
    }

    /** POST /api/solicitacoes/{id}/avaliacao — protegido (ong dona). 201 · 403 · 404 · 409 · 422. */
    public function avaliar()
    {
        $usuario = $this->exigirAutenticacao();
        $repo    = new SolicitacaoRepository();
        $id      = (int) $this->param('id');

        $linha = $repo->buscarLinha($id);
        if (!$linha) {
            throw new ApiException('Solicitação não encontrada.', 404);
        }
        $ongId = $this->exigirOngDona($usuario, $repo, $id);

        if ($repo->avaliacaoExiste($id)) {
            throw new ApiException('Esta solicitação já possui avaliação (registro imutável).', 409);
        }

        $body = $this->body();
        $this->exigirCampos($body, ['tipo_moradia', 'parecer', 'resultado']);

        if (!in_array($body['tipo_moradia'], self::TIPOS_MORADIA, true)) {
            throw new ApiException('tipo_moradia inválido. Use: ' . implode(', ', self::TIPOS_MORADIA) . '.', 422);
        }
        if (!in_array($body['resultado'], self::RESULTADOS, true)) {
            throw new ApiException('resultado inválido. Use: ' . implode(', ', self::RESULTADOS) . '.', 422);
        }

        $criadoPor = $this->nomeDaOng($ongId);
        $repo->criarAvaliacao($id, $body, $criadoPor);

        $this->json($repo->buscarAvaliacao($id), 201);
    }

    /** GET /api/solicitacoes/{id}/termo — protegido (adotante dono). 200 · 403 · 404. */
    public function termo()
    {
        $usuario = $this->exigirAutenticacao();
        $repo    = new SolicitacaoRepository();
        $id      = (int) $this->param('id');

        $linha = $repo->buscarLinha($id);
        if (!$linha) {
            throw new ApiException('Solicitação não encontrada.', 404);
        }
        $this->exigirAdotanteDono($usuario, $repo, $linha);

        $this->json($repo->montarTermo($repo->termoGarantir($id)));
    }

    /** POST /api/solicitacoes/{id}/termo/assinar — protegido (adotante dono). 200 · 400 · 403 · 404 · 422. */
    public function assinarTermo()
    {
        $usuario = $this->exigirAutenticacao();
        $repo    = new SolicitacaoRepository();
        $id      = (int) $this->param('id');

        $linha = $repo->buscarLinha($id);
        if (!$linha) {
            throw new ApiException('Solicitação não encontrada.', 404);
        }
        $this->exigirAdotanteDono($usuario, $repo, $linha);

        $body = $this->body();
        if (empty($body['aceite'])) {
            throw new ApiException('É necessário aceitar o termo (aceite=true).', 422);
        }
        // "Já assinado" antes do status: a própria assinatura move o status p/ Concluído,
        // então este check precisa vir primeiro para ser alcançável.
        if ($repo->termoAssinado($id)) {
            throw new ApiException('Este termo já foi assinado.', 400);
        }
        if ($linha['status'] !== 'Aprovado') {
            throw new ApiException('O termo só pode ser assinado quando a solicitação estiver Aprovada.', 422);
        }

        // IP e user-agent são capturados SERVER-SIDE (Lei 14.063/2020). Nunca confiar no cliente.
        $ip        = $this->ip();
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;

        $this->json($repo->assinarTermo($id, $ip, $userAgent), 200);
    }

    // ----------------------------------------------------------------- helpers de acesso

    /** Exige que o usuário seja adotante; retorna o adotante_id. */
    private function exigirAdotante($usuario, SolicitacaoRepository $repo)
    {
        if (!isset($usuario->tipo_usuario) || $usuario->tipo_usuario !== 'adotante') {
            throw new ApiException('Apenas adotantes podem realizar esta operação.', 403);
        }
        $adotanteId = $repo->adotanteIdPorLogin((int) $usuario->sub);
        if (!$adotanteId) {
            throw new ApiException('Perfil de adotante não encontrado para este login.', 404);
        }
        return $adotanteId;
    }

    /** Exige que o usuário seja ONG; retorna o ong_id. */
    private function exigirOng($usuario, SolicitacaoRepository $repo)
    {
        if (!isset($usuario->tipo_usuario) || $usuario->tipo_usuario !== 'ong') {
            throw new ApiException('Apenas ONGs podem realizar esta operação.', 403);
        }
        $ongId = $repo->ongIdPorLogin((int) $usuario->sub);
        if (!$ongId) {
            throw new ApiException('Perfil de ONG não encontrado para este login.', 404);
        }
        return $ongId;
    }

    /** Exige ONG dona do animal da solicitação; retorna o ong_id. */
    private function exigirOngDona($usuario, SolicitacaoRepository $repo, $solicitacaoId)
    {
        $ongId = $this->exigirOng($usuario, $repo);
        if (!$repo->ongPossuiAnimalDaSolicitacao($solicitacaoId, $ongId)) {
            throw new ApiException('Sem permissão sobre esta solicitação.', 403);
        }
        return $ongId;
    }

    /** Exige adotante dono da solicitação. */
    private function exigirAdotanteDono($usuario, SolicitacaoRepository $repo, array $linha)
    {
        $adotanteId = $this->exigirAdotante($usuario, $repo);
        if ((int) $linha['fk_adotante_id'] !== (int) $adotanteId) {
            throw new ApiException('Sem permissão sobre esta solicitação.', 403);
        }
    }

    /**
     * Acesso de leitura: adotante dono OU ONG dona do animal.
     * Retorna true se o solicitante é a ONG (para enriquecer com dados do adotante).
     */
    private function garantirAcesso($usuario, SolicitacaoRepository $repo, $solicitacaoId, array $linha)
    {
        $tipo = isset($usuario->tipo_usuario) ? $usuario->tipo_usuario : null;

        if ($tipo === 'adotante') {
            $adotanteId = $repo->adotanteIdPorLogin((int) $usuario->sub);
            if ($adotanteId && (int) $linha['fk_adotante_id'] === (int) $adotanteId) {
                return false;
            }
        } elseif ($tipo === 'ong') {
            $ongId = $repo->ongIdPorLogin((int) $usuario->sub);
            if ($ongId && $repo->ongPossuiAnimalDaSolicitacao($solicitacaoId, $ongId)) {
                return true;
            }
        }

        throw new ApiException('Sem permissão sobre esta solicitação.', 403);
    }

    private function nomeDaOng($ongId)
    {
        $ong = (new \App\Api\Repository\OngRepository())->buscarPorId($ongId);
        return $ong ? $ong['nome'] : 'ONG';
    }
}
