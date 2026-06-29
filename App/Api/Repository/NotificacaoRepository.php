<?php

namespace App\Api\Repository;

use FW\DB\Connection;

/**
 * Acesso a dados de Notificações in-app (seção C.9).
 * Tabela: notificacao. Sintaxe PHP 7.0.
 *
 * De-para: destino_tab/destino_tela/destino_params (TEXT/JSON) → objeto destino { tab, tela, params }.
 * lida (TINYINT 0/1) → bool no app.
 */
class NotificacaoRepository
{
 /** @var \PDO */
 private $conn;

 public function __construct()
 {
  $this->conn = (new Connection())->getConn();
 }

 /** Lista notificações do login, mais recentes primeiro. Array puro. */
 public function listarPorLogin($loginId): array
 {
  $stmt = $this->conn->prepare(
   "SELECT * FROM notificacao WHERE fk_login_id = :loginId ORDER BY data DESC"
  );
  $stmt->execute([':loginId' => (int) $loginId]);

  return array_map([$this, 'montar'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
 }

 /**
  * Marca notificação como lida se pertence ao login.
  * Retorna true se atualizou, false caso contrário (404).
  */
 public function marcarLida($id, $loginId)
 {
  $stmt = $this->conn->prepare(
   "UPDATE notificacao SET lida = 1 WHERE id = :id AND fk_login_id = :loginId"
  );
  $stmt->execute([':id' => (int) $id, ':loginId' => (int) $loginId]);

  return $stmt->rowCount() > 0;
 }

 /**
  * Cria uma notificação para uso por outros módulos.
  *
  * @param int         $loginId  Destinatário (fk_login_id).
  * @param string      $titulo   Título da notificação.
  * @param string      $mensagem Corpo da mensagem.
  * @param string|null $tipo     Tipo (ex.: 'adocao', 'transferencia', etc.).
  * @param array|null  $destino  ['tab' => ..., 'tela' => ..., 'params' => ...] ou null.
  * @return int         ID inserido.
  */
 public function criar($loginId, $titulo, $mensagem, $tipo = null, $destino = null)
 {
  $destinoTab    = null;
  $destinoTela   = null;
  $destinoParams = null;

  if (is_array($destino)) {
   $destinoTab    = isset($destino['tab'])   ? $destino['tab']   : null;
   $destinoTela   = isset($destino['tela'])  ? $destino['tela']  : null;
   $destinoParams = isset($destino['params']) && $destino['params'] !== null
    ? json_encode($destino['params'])
    : null;
  }

  $stmt = $this->conn->prepare(
   "INSERT INTO notificacao
                (fk_login_id, titulo, mensagem, tipo, destino_tab, destino_tela, destino_params)
             VALUES
                (:loginId, :titulo, :mensagem, :tipo, :destinoTab, :destinoTela, :destinoParams)"
  );
  $stmt->execute([
   ':loginId'      => (int) $loginId,
   ':titulo'       => $titulo,
   ':mensagem'     => $mensagem,
   ':tipo'         => $tipo,
   ':destinoTab'   => $destinoTab,
   ':destinoTela'  => $destinoTela,
   ':destinoParams' => $destinoParams,
  ]);

  return (int) $this->conn->lastInsertId();
 }

    // ----------------------------------------------------------------- helpers

 /** Monta o shape de Notificação (de-para banco → app). */
 private function montar(array $r): array
 {
  $destino = null;
  if (!empty($r['destino_tab']) || !empty($r['destino_tela'])) {
   $params = null;
   if (!empty($r['destino_params'])) {
    $dec = json_decode($r['destino_params'], true);
    if (is_array($dec)) {
     $params = $dec;
    }
   }
   $destino = [
    'tab'   => $r['destino_tab'],
    'tela'  => $r['destino_tela'],
    'params' => $params,
   ];
  }

  return [
   'id'      => (int) $r['id'],
   'titulo'  => $r['titulo'],
   'mensagem' => $r['mensagem'],
   'data'    => $r['data'],
   'lida'    => (bool) $r['lida'],
   'tipo'    => $r['tipo'],
   'destino' => $destino,
  ];
 }
}
