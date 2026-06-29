<?php

namespace App\Api\Controller;

use App\Api\ApiController;
use App\Api\ApiException;
use App\Api\Repository\NotificacaoRepository;

/**
 * Módulo de Notificações in-app (seção C.9 da especificação).
 * Endpoints: listar (pessoa logada) e marcar-lida.
 * Sintaxe PHP 7.0.
 */
class NotificacaoController extends ApiController
{
 /** GET /notificacoes — 200 array de notificações do usuário logado. */
 public function listar()
 {
  $usuario = $this->exigirAutenticacao();
  $repo    = new NotificacaoRepository();
  $this->json($repo->listarPorLogin($usuario->sub));
 }

 /** PATCH /notificacoes/{id}/marcar-lida — 200 { id, lida:true } · 404. */
 public function marcarLida()
 {
  $usuario = $this->exigirAutenticacao();
  $repo    = new NotificacaoRepository();
  $id      = (int) $this->param('id');

  $ok = $repo->marcarLida($id, $usuario->sub);
  if (!$ok) {
   throw new ApiException('Notificação não encontrada.', 404);
  }

  $this->json(['id' => $id, 'lida' => true]);
 }
}
