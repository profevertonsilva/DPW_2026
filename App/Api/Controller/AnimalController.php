<?php

namespace App\Api\Controller;

use App\Api\ApiController;
use App\Api\ApiException;
use App\Api\Repository\AnimalRepository;

/**
 * Módulo de Animais (seção C.2 da especificação).
 * Endpoints: listar, meus, detalhe, historico, criar, atualizar, especies, racas.
 *
 * Convenção de resposta (conforme spec): listas retornam ARRAY PURO e o detalhe
 * retorna o OBJETO PURO (sem envelope { animal: ... }). Sintaxe PHP 7.0.
 */
class AnimalController extends ApiController
{
    const SEXOS_VALIDOS  = ['m', 'f', 'n/a'];
    const PORTES_VALIDOS = ['pequeno', 'medio', 'grande', 'gigante'];
    const STATUS_VALIDOS = ['disponivel', 'adotado', 'em_tratamento', 'reservado'];

    /**
     * GET /api/animais — query especie?, porte?, sexo?, localizacao?, busca?.
     * 200 array de Animal.
     */
    public function listar()
    {
        $filtros = [];
        foreach (['especie', 'porte', 'sexo', 'localizacao', 'busca'] as $campo) {
            $val = $this->query($campo);
            if ($val !== null && trim($val) !== '') {
                $filtros[$campo] = trim($val);
            }
        }

        $repo = new AnimalRepository();
        $this->json($repo->listar($filtros));
    }

    /**
     * GET /api/animais/meus — protegido (ONG). 200 array de Animal da ONG logada.
     * DEVE ser registrado antes de /animais/{id} nas rotas.
     */
    public function meus()
    {
        $usuario = $this->exigirAutenticacao();
        $this->exigirOng($usuario);

        $repo  = new AnimalRepository();
        $ongId = $repo->ongIdPorLoginId((int) $usuario->sub);
        if (!$ongId) {
            throw new ApiException('Perfil de ONG não encontrado para este login.', 404);
        }

        $this->json($repo->listarPorOng($ongId));
    }

    /** GET /api/animais/{id} — 200 Animal · 404. */
    public function detalhe()
    {
        $repo   = new AnimalRepository();
        $animal = $repo->buscarPorId((int) $this->param('id'));

        if (!$animal) {
            throw new ApiException('Animal não encontrado.', 404);
        }

        $this->json($animal);
    }

    /** GET /api/animais/{id}/historico — 200 array (auditoria). */
    public function historico()
    {
        $id   = (int) $this->param('id');
        $repo = new AnimalRepository();

        if (!$repo->existe($id)) {
            throw new ApiException('Animal não encontrado.', 404);
        }

        $this->json($repo->historico($id));
    }

    /** POST /api/animais — protegido (ONG). 201 Animal · 403. */
    public function criar()
    {
        $usuario = $this->exigirAutenticacao();
        $this->exigirOng($usuario);

        $body = $this->body();
        $this->exigirCampos($body, ['nome', 'sexo', 'porte', 'fk_especie_id']);
        $this->validarEnums($body);

        $repo  = new AnimalRepository();
        $ongId = $repo->ongIdPorLoginId((int) $usuario->sub);
        if (!$ongId) {
            throw new ApiException('Perfil de ONG não encontrado para este login.', 404);
        }

        $animalId = $repo->criar($body, $ongId);
        $this->json($repo->buscarPorId($animalId), 201);
    }

    /** PUT /api/animais/{id} — protegido (ONG dona). 200 Animal · 403 · 404. */
    public function atualizar()
    {
        $usuario  = $this->exigirAutenticacao();
        $this->exigirOng($usuario);

        $animalId = (int) $this->param('id');
        $repo     = new AnimalRepository();

        if (!$repo->existe($animalId)) {
            throw new ApiException('Animal não encontrado.', 404);
        }

        $ongId = $repo->ongIdPorLoginId((int) $usuario->sub);
        if (!$ongId || !$repo->animalPertenceAOng($animalId, $ongId)) {
            throw new ApiException('Sem permissão para editar este animal.', 403);
        }

        $body = $this->body();
        $this->validarEnums($body);

        $repo->atualizar($animalId, $body);
        $this->json($repo->buscarPorId($animalId));
    }

    /** GET /api/especies — 200 [{ id, nome }]. */
    public function especies()
    {
        $repo = new AnimalRepository();
        $this->json($repo->especies());
    }

    /** GET /api/racas?especie_id= — 200 [{ id, nome, fk_especie_id }]. */
    public function racas()
    {
        $repo      = new AnimalRepository();
        $especieId = $this->query('especie_id');
        $this->json($repo->racas($especieId !== null ? (int) $especieId : null));
    }

    // ----------------------------------------------------------------- helpers

    private function exigirOng($usuario)
    {
        if (!isset($usuario->tipo_usuario) || $usuario->tipo_usuario !== 'ong') {
            throw new ApiException('Apenas ONGs podem realizar esta operação.', 403);
        }
    }

    private function validarEnums(array $body)
    {
        if (isset($body['sexo']) && !in_array($body['sexo'], self::SEXOS_VALIDOS, true)) {
            throw new ApiException('Valor inválido para sexo. Use: m, f ou n/a.', 422);
        }
        if (isset($body['porte']) && !in_array($body['porte'], self::PORTES_VALIDOS, true)) {
            throw new ApiException('Valor inválido para porte. Use: pequeno, medio, grande ou gigante.', 422);
        }
        if (isset($body['status']) && !in_array($body['status'], self::STATUS_VALIDOS, true)) {
            throw new ApiException('Valor inválido para status. Use: disponivel, adotado, em_tratamento ou reservado.', 422);
        }
    }
}
