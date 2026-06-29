<?php

namespace App\Api\Controller;

use App\Api\ApiController;
use App\Api\ApiException;
use App\Api\Repository\ClinicaRepository;

/**
 * Módulo de Clínicas Veterinárias (seção C.12 da especificação).
 * Endpoints: listar, detalhe, criar (vet), minhasClinicas (vet),
 * associarClinica (vet), desassociarClinica (vet).
 *
 * Listas retornam ARRAY PURO e o detalhe retorna o OBJETO PURO (sem envelope).
 * Sintaxe PHP 7.0.
 */
class ClinicaController extends ApiController
{
 /** GET /clinicas — qualquer autenticado. 200 array de Clinica. */
 public function listar()
 {
  $this->exigirAutenticacao();
  $repo = new ClinicaRepository();
  $this->json($repo->listar());
 }

 /** GET /clinicas/{id} — 200 Clinica · 404. */
 public function detalhe()
 {
  $this->exigirAutenticacao();
  $repo = new ClinicaRepository();
  $clinica = $repo->buscarPorId((int) $this->param('id'));

  if (!$clinica) {
   throw new ApiException('Clínica não encontrada.', 404);
  }

  $this->json($clinica);
 }

 /** POST /clinicas — somente veterinário. 201 · 400 · 422. */
 public function criar()
 {
  $usuario = $this->exigirAutenticacao();
  $this->exigirVeterinario($usuario);

  $body = $this->body();
  $this->exigirCampos($body, ['nome']);

  $repo = new ClinicaRepository();
  $id = $repo->criar($body);
  $clinica = $repo->buscarPorId($id);

  $this->json($clinica, 201);
 }

 /** GET /veterinario/clinicas — somente vet. 200 array de Clinica. */
 public function minhasClinicas()
 {
  $usuario = $this->exigirAutenticacao();
  $vetId = $this->exigirVeterinario($usuario);

  $repo = new ClinicaRepository();
  $this->json($repo->doVeterinario($vetId));
 }

 /** POST /veterinario/clinicas/{clinicaId} — associa vet a clínica. Idempotente. 201 · 200 · 404. */
 public function associarClinica()
 {
  $usuario = $this->exigirAutenticacao();
  $vetId = $this->exigirVeterinario($usuario);

  $clinicaId = (int) $this->param('clinicaId');
  $repo = new ClinicaRepository();

  if (!$repo->existe($clinicaId)) {
   throw new ApiException('Clínica não encontrada.', 404);
  }

  if ($repo->vinculoExiste($vetId, $clinicaId)) {
   $this->json([
    'clinica_id'     => $clinicaId,
    'veterinario_id' => $vetId,
    'associado'      => true,
   ]);
   return;
  }

  $repo->associar($vetId, $clinicaId);
  $this->json([
   'clinica_id'     => $clinicaId,
   'veterinario_id' => $vetId,
   'associado'      => true,
  ], 201);
 }

 /** DELETE /veterinario/clinicas/{clinicaId} — desassocia vet de clínica. 200 · 404. */
 public function desassociarClinica()
 {
  $usuario = $this->exigirAutenticacao();
  $vetId = $this->exigirVeterinario($usuario);

  $clinicaId = (int) $this->param('clinicaId');
  $repo = new ClinicaRepository();

  if (!$repo->vinculoExiste($vetId, $clinicaId)) {
   throw new ApiException('Vínculo não encontrado.', 404);
  }

  $repo->desassociar($vetId, $clinicaId);
  $this->json([
   'clinica_id'     => $clinicaId,
   'veterinario_id' => $vetId,
   'associado'      => false,
  ]);
 }

    // ----------------------------------------------------------------- helpers

 /**
  * Exige que o usuário autenticado seja veterinário.
  * Retorna o veterinario_id. Lança 403 se não for vet, 404 se perfil não existe.
  */
 private function exigirVeterinario($usuario)
 {
  if (!isset($usuario->tipo_usuario) || $usuario->tipo_usuario !== 'veterinario') {
   throw new ApiException('Apenas veterinários podem realizar esta operação.', 403);
  }

  $repo = new ClinicaRepository();
  $vetId = $repo->vetIdPorLogin((int) $usuario->sub);

  if (!$vetId) {
   throw new ApiException('Perfil de veterinário não encontrado para este login.', 404);
  }

  return $vetId;
 }
}
