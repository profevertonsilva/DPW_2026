<?php

namespace App\Api\Controller;

use App\Api\ApiController;
use App\Api\ApiException;
use App\Api\Repository\TransferenciaRepository;
use App\Api\Repository\AnimalRepository;

/**
 * Módulo de Transferência de Responsabilidade (seção C.8).
 *
 * Endpoints protegidos (JWT): criar e listar transferências de um animal.
 * Apenas a ONG dona do animal pode transferir. Append-only. Sintaxe PHP 7.0.
 */
class TransferenciaController extends ApiController
{
 /**
  * POST /animais/{id}/transferencias — 201 · 403 · 404 · 422.
  * ONG dona transfere a responsabilidade do animal para outro login.
  */
 public function criar()
 {
  $usuario   = $this->exigirAutenticacao();
  $ongId     = $this->exigirOng($usuario);
  $animalId  = (int) $this->param('id');

  $animalRepo = new AnimalRepository();

  if (!$animalRepo->existe($animalId)) {
   throw new ApiException('Animal não encontrado.', 404);
  }

  if (!$animalRepo->animalPertenceAOng($animalId, $ongId)) {
   throw new ApiException('Este animal não pertence à sua ONG.', 403);
  }

  $body = $this->body();
  $this->exigirCampos($body, ['para_usuario_id']);

  $paraUsuarioId = (int) $body['para_usuario_id'];

  $repo = new TransferenciaRepository();

  if (!$repo->loginExiste($paraUsuarioId)) {
   throw new ApiException('Destinatário (login) não encontrado.', 422);
  }

  // Resolve nomes
  $deUsuarioNome = $this->nomeDaOng($ongId);

  $paraUsuarioNome = '';
  if (!empty($body['para_usuario_nome'])) {
   $paraUsuarioNome = $body['para_usuario_nome'];
  } else {
   $paraUsuarioNome = $repo->nomePorLoginId($paraUsuarioId);
  }

  $motivo = isset($body['motivo']) ? $body['motivo'] : null;

  $transferencia = $repo->criar(
   $animalId,
   $ongId,
   $deUsuarioNome,
   $paraUsuarioId,
   $paraUsuarioNome,
   $motivo
  );

  $this->json($transferencia, 201);
 }

 /**
  * GET /animais/{id}/transferencias — 200 array · 403 · 404.
  * Lista transferências do animal (mais recentes primeiro).
  */
 public function listar()
 {
  $usuario  = $this->exigirAutenticacao();
  $ongId    = $this->exigirOng($usuario);
  $animalId = (int) $this->param('id');

  $animalRepo = new AnimalRepository();

  if (!$animalRepo->existe($animalId)) {
   throw new ApiException('Animal não encontrado.', 404);
  }

  if (!$animalRepo->animalPertenceAOng($animalId, $ongId)) {
   throw new ApiException('Este animal não pertence à sua ONG.', 403);
  }

  $repo = new TransferenciaRepository();
  $this->json($repo->listarPorAnimal($animalId));
 }

    // ----------------------------------------------------------------- helpers

 /**
  * Exige que o usuário autenticado seja ONG. Retorna o ong_id.
  */
 private function exigirOng($usuario)
 {
  if (!isset($usuario->tipo_usuario) || $usuario->tipo_usuario !== 'ong') {
   throw new ApiException('Apenas ONGs podem realizar esta operação.', 403);
  }
  $animalRepo = new AnimalRepository();
  $ongId = $animalRepo->ongIdPorLoginId((int) $usuario->sub);
  if (!$ongId) {
   throw new ApiException('Perfil de ONG não encontrado para este login.', 404);
  }
  return $ongId;
 }

 /**
  * Resolve o nome da ONG pelo id. Fallback 'ONG'.
  */
 private function nomeDaOng($ongId)
 {
  $repo = new \App\Api\Repository\OngRepository();
  $ong  = $repo->buscarPorId($ongId);
  return $ong ? $ong['nome'] : 'ONG';
 }
}
