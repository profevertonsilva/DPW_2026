<?php

namespace App\Api\Repository;

use FW\DB\Connection;

/**
 * Acesso a dados do módulo de Transferência de Responsabilidade (seção C.8).
 * Tabela: transferencia (append-only). Sintaxe PHP 7.0.
 */
class TransferenciaRepository
{
 /** @var \PDO */
 private $conn;

 public function __construct()
 {
  $this->conn = (new Connection())->getConn();
 }

 /**
  * Insere uma transferência. Retorna o registro criado (shape).
  *
  * @param int    $animalId
  * @param int    $deUsuarioId
  * @param string $deUsuarioNome
  * @param int    $paraUsuarioId
  * @param string $paraUsuarioNome
  * @param string|null $motivo
  * @return array
  */
 public function criar($animalId, $deUsuarioId, $deUsuarioNome, $paraUsuarioId, $paraUsuarioNome, $motivo)
 {
  $stmt = $this->conn->prepare(
   "INSERT INTO transferencia
                (fk_animal_id, de_usuario_id, de_usuario_nome, para_usuario_id, para_usuario_nome, motivo, data)
             VALUES
                (:animal_id, :de_id, :de_nome, :para_id, :para_nome, :motivo, NOW())"
  );
  $stmt->execute([
   ':animal_id' => (int) $animalId,
   ':de_id'     => (int) $deUsuarioId,
   ':de_nome'   => $deUsuarioNome,
   ':para_id'   => (int) $paraUsuarioId,
   ':para_nome' => $paraUsuarioNome,
   ':motivo'    => $motivo,
  ]);

  $id = (int) $this->conn->lastInsertId();
  return $this->buscarPorId($id);
 }

 /**
  * Busca transferência por id. Retorna shape ou null.
  */
 public function buscarPorId($id)
 {
  $stmt = $this->conn->prepare(
   "SELECT id, fk_animal_id, de_usuario_id, de_usuario_nome,
                    para_usuario_id, para_usuario_nome, motivo, data
             FROM transferencia WHERE id = :id LIMIT 1"
  );
  $stmt->execute([':id' => (int) $id]);
  $row = $stmt->fetch(\PDO::FETCH_ASSOC);

  return $row ? $this->montarTransferencia($row) : null;
 }

 /**
  * Lista transferências de um animal (mais recentes primeiro).
  *
  * @param int $animalId
  * @return array
  */
 public function listarPorAnimal($animalId)
 {
  $stmt = $this->conn->prepare(
   "SELECT id, fk_animal_id, de_usuario_id, de_usuario_nome,
                    para_usuario_id, para_usuario_nome, motivo, data
             FROM transferencia
             WHERE fk_animal_id = :animal_id
             ORDER BY data DESC"
  );
  $stmt->execute([':animal_id' => (int) $animalId]);

  return array_map([$this, 'montarTransferencia'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
 }

 /**
  * Resolve o nome de um usuário por login_id (COALESCE perfis).
  * Tenta adotante, ong e veterinario; fallback ''.
  *
  * @param int $loginId
  * @return string
  */
 public function nomePorLoginId($loginId)
 {
  $stmt = $this->conn->prepare(
   "SELECT COALESCE(
                (SELECT a.nome FROM adotante a WHERE a.fk_login_id = :id LIMIT 1),
                (SELECT o.nome FROM ong o WHERE o.fk_login_id = :id LIMIT 1),
                (SELECT v.nome FROM veterinario v WHERE v.fk_login_id = :id LIMIT 1),
                ''
             ) AS nome"
  );
  $stmt->execute([':id' => (int) $loginId]);
  $nome = $stmt->fetchColumn();
  return $nome !== false ? $nome : '';
 }

 /**
  * Verifica se um login_id existe na tabela login.
  *
  * @param int $loginId
  * @return bool
  */
 public function loginExiste($loginId)
 {
  $stmt = $this->conn->prepare('SELECT 1 FROM login WHERE id = :id LIMIT 1');
  $stmt->execute([':id' => (int) $loginId]);
  return (bool) $stmt->fetchColumn();
 }

    // ----------------------------------------------------------------- helpers

 /**
  * Monta o shape de transferência a partir da linha do banco.
  */
 private function montarTransferencia(array $t)
 {
  return [
   'id'               => (int) $t['id'],
   'fk_animal_id'     => (int) $t['fk_animal_id'],
   'de_usuario_id'    => (int) $t['de_usuario_id'],
   'de_usuario_nome'  => $t['de_usuario_nome'],
   'para_usuario_id'  => (int) $t['para_usuario_id'],
   'para_usuario_nome' => $t['para_usuario_nome'],
   'motivo'           => $t['motivo'],
   'data'             => $t['data'],
  ];
 }
}
