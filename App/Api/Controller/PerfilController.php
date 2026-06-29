<?php

namespace App\Api\Controller;

use App\Api\ApiController;
use App\Api\ApiException;
use App\Api\Validador;
use App\Api\Repository\PerfilRepository;

/**
 * Módulo de Perfis de Usuário (seção C.11 da especificação).
 *
 * Endpoints: GET/PUT para perfil de adotante, ong e veterinário.
 * PATCH semântico: apenas campos enviados no body são atualizados.
 * Listas retornam ARRAY PURO; detalhe retorna OBJETO PURO (sem envelope).
 * Sintaxe PHP 7.0.
 */
class PerfilController extends ApiController
{
    // -------------------------------------------------------------- adotante

 /** GET /adotante/perfil — protegido (adotante). 200 · 403 · 404. */
 public function perfilAdotante()
 {
  $usuario    = $this->exigirAutenticacao();
  $repo       = new PerfilRepository();
  $adotanteId = $this->exigirAdotante($usuario, $repo);

  $perfil = $repo->perfilAdotante($adotanteId);
  if (!$perfil) {
   throw new ApiException('Perfil de adotante não encontrado.', 404);
  }

  $this->json($perfil);
 }

 /** PUT /adotante/perfil — protegido (adotante). 200 · 400 · 403 · 404 · 422. */
 public function atualizarAdotante()
 {
  $usuario    = $this->exigirAutenticacao();
  $repo       = new PerfilRepository();
  $adotanteId = $this->exigirAdotante($usuario, $repo);
  $loginId    = (int) $usuario->sub;
  $body       = $this->body();

  // Validação de email, se enviado
  if (array_key_exists('email', $body)) {
   if (!Validador::email($body['email'])) {
    throw new ApiException('Formato de e-mail inválido.', 422);
   }
   if ($repo->emailExisteEmOutroLogin($body['email'], $loginId)) {
    throw new ApiException('Este e-mail já está em uso por outra conta.', 422);
   }
  }

  $perfil = $repo->atualizarAdotante($adotanteId, $body, $loginId);
  if (!$perfil) {
   throw new ApiException('Nenhum campo válido informado para atualização.', 400);
  }

  $this->json($perfil);
 }

    // -------------------------------------------------------------- ong

 /** GET /ong/perfil — protegido (ong). 200 · 403 · 404. */
 public function perfilOng()
 {
  $usuario = $this->exigirAutenticacao();
  $repo    = new PerfilRepository();
  $ongId   = $this->exigirOng($usuario, $repo);

  $perfil = $repo->perfilOng($ongId);
  if (!$perfil) {
   throw new ApiException('Perfil de ONG não encontrado.', 404);
  }

  $this->json($perfil);
 }

 /** PUT /ong/perfil — protegido (ong). 200 · 400 · 403 · 404 · 422. */
 public function atualizarOng()
 {
  $usuario = $this->exigirAutenticacao();
  $repo    = new PerfilRepository();
  $ongId   = $this->exigirOng($usuario, $repo);
  $loginId = (int) $usuario->sub;
  $body    = $this->body();

  if (array_key_exists('email', $body)) {
   if (!Validador::email($body['email'])) {
    throw new ApiException('Formato de e-mail inválido.', 422);
   }
   if ($repo->emailExisteEmOutroLogin($body['email'], $loginId)) {
    throw new ApiException('Este e-mail já está em uso por outra conta.', 422);
   }
  }

  $perfil = $repo->atualizarOng($ongId, $body, $loginId);
  if (!$perfil) {
   throw new ApiException('Nenhum campo válido informado para atualização.', 400);
  }

  $this->json($perfil);
 }

    // -------------------------------------------------------------- veterinario

 /** GET /veterinario/perfil — protegido (veterinario). 200 · 403 · 404. */
 public function perfilVeterinario()
 {
  $usuario = $this->exigirAutenticacao();
  $repo    = new PerfilRepository();
  $vetId   = $this->exigirVeterinario($usuario, $repo);

  $perfil = $repo->perfilVeterinario($vetId);
  if (!$perfil) {
   throw new ApiException('Perfil de veterinário não encontrado.', 404);
  }

  $this->json($perfil);
 }

 /** PUT /veterinario/perfil — protegido (veterinario). 200 · 400 · 403 · 404 · 422. */
 public function atualizarVeterinario()
 {
  $usuario = $this->exigirAutenticacao();
  $repo    = new PerfilRepository();
  $vetId   = $this->exigirVeterinario($usuario, $repo);
  $loginId = (int) $usuario->sub;
  $body    = $this->body();

  if (array_key_exists('email', $body)) {
   if (!Validador::email($body['email'])) {
    throw new ApiException('Formato de e-mail inválido.', 422);
   }
   if ($repo->emailExisteEmOutroLogin($body['email'], $loginId)) {
    throw new ApiException('Este e-mail já está em uso por outra conta.', 422);
   }
  }

  $perfil = $repo->atualizarVeterinario($vetId, $body, $loginId);
  if (!$perfil) {
   throw new ApiException('Nenhum campo válido informado para atualização.', 400);
  }

  $this->json($perfil);
 }

    // ----------------------------------------------------------------- helpers de acesso

 /** Exige que o usuário seja adotante; retorna o adotante_id. */
 private function exigirAdotante($usuario, PerfilRepository $repo)
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
 private function exigirOng($usuario, PerfilRepository $repo)
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

 /** Exige que o usuário seja veterinário; retorna o veterinario_id. */
 private function exigirVeterinario($usuario, PerfilRepository $repo)
 {
  if (!isset($usuario->tipo_usuario) || $usuario->tipo_usuario !== 'veterinario') {
   throw new ApiException('Apenas veterinários podem realizar esta operação.', 403);
  }
  $vetId = $repo->veterinarioIdPorLogin((int) $usuario->sub);
  if (!$vetId) {
   throw new ApiException('Perfil de veterinário não encontrado para este login.', 404);
  }
  return $vetId;
 }
}
