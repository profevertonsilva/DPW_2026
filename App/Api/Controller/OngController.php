<?php

namespace App\Api\Controller;

use App\Api\ApiController;
use App\Api\ApiException;
use App\Api\Repository\OngRepository;
use App\Api\Repository\AnimalRepository;

/**
 * Módulo de ONGs (seção C.3 da especificação).
 * Endpoints: listar, detalhe e animais da ONG.
 *
 * Listas retornam ARRAY PURO e o detalhe retorna o OBJETO PURO (sem envelope).
 * Sintaxe PHP 7.0.
 */
class OngController extends ApiController
{
    /** GET /api/ongs — 200 array de ONG. */
    public function listar()
    {
        $repo = new OngRepository();
        $this->json($repo->listar());
    }

    /** GET /api/ongs/{id} — 200 ONG · 404. */
    public function detalhe()
    {
        $repo = new OngRepository();
        $ong  = $repo->buscarPorId((int) $this->param('id'));

        if (!$ong) {
            throw new ApiException('ONG não encontrada.', 404);
        }

        $this->json($ong);
    }

    /** GET /api/ongs/{id}/animais — 200 array de Animal · 404. */
    public function animais()
    {
        $ongId   = (int) $this->param('id');
        $ongRepo = new OngRepository();

        if (!$ongRepo->existe($ongId)) {
            throw new ApiException('ONG não encontrada.', 404);
        }

        $animalRepo = new AnimalRepository();
        $this->json($animalRepo->listarPorOng($ongId));
    }
}
