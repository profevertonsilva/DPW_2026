<?php

namespace App\Api\Repository;

use FW\DB\Connection;

/**
 * Acesso a dados do módulo de Perfis (seção C.11).
 *
 * Busca e atualização de perfil para adotante, ong e veterinário.
 * PATCH semântico: apenas campos enviados são atualizados.
 * De-para: foto↔avatar, telefone_1↔veterinario.telefone, descricao↔ong.bio,
 *          email/status(conta)↔login. Sintaxe PHP 7.0.
 */
class PerfilRepository
{
 /** @var \PDO */
 private $conn;

 public function __construct()
 {
  $this->conn = (new Connection())->getConn();
 }

    // -------------------------------------------------------------- resolução de ids

 /** id do adotante a partir do login, ou null. */
 public function adotanteIdPorLogin($loginId)
 {
  return $this->escalarId('SELECT id FROM adotante WHERE fk_login_id = :id LIMIT 1', $loginId);
 }

 /** id da ONG a partir do login, ou null. */
 public function ongIdPorLogin($loginId)
 {
  return $this->escalarId('SELECT id FROM ong WHERE fk_login_id = :id LIMIT 1', $loginId);
 }

 /** id do veterinário a partir do login, ou null. */
 public function veterinarioIdPorLogin($loginId)
 {
  return $this->escalarId('SELECT id FROM veterinario WHERE fk_login_id = :id LIMIT 1', $loginId);
 }

    // -------------------------------------------------------------- adotante

 /** Perfil do adotante (shape spec) ou null. */
 public function perfilAdotante($adotanteId)
 {
  $stmt = $this->conn->prepare(
   "SELECT a.*, l.email AS login_email, l.status AS login_status
             FROM adotante a
             LEFT JOIN login l ON l.id = a.fk_login_id
             WHERE a.id = :id LIMIT 1"
  );
  $stmt->execute([':id' => (int) $adotanteId]);
  $row = $stmt->fetch(\PDO::FETCH_ASSOC);

  return $row ? $this->montarAdotante($row) : null;
 }

 /** Atualiza campos do adotante (PATCH) + login.email se enviado. */
 public function atualizarAdotante($adotanteId, array $d, $loginId)
 {
  $colunasPermitidas = [
   'nome',
   'cpf',
   'data_nascimento',
   'cep',
   'numero',
   'bairro',
   'cidade',
   'estado',
   'complemento',
   'logradouro',
   'telefone_1',
   'telefone_2',
  ];

  $sets   = [];
  $params = [':id' => (int) $adotanteId];

  foreach ($colunasPermitidas as $col) {
   if (array_key_exists($col, $d)) {
    $sets[]            = "`{$col}` = :{$col}";
    $params[":{$col}"] = $d[$col];
   }
  }

  // de-para: foto (app) → avatar (banco)
  if (array_key_exists('foto', $d)) {
   $sets[]       = '`avatar` = :avatar';
   $params[':avatar'] = $d['foto'];
  }

  $temPerfil = !empty($sets);
  $temEmail  = array_key_exists('email', $d);

  if (!$temPerfil && !$temEmail) {
   return null;
  }

  $this->conn->beginTransaction();
  try {
   if ($temPerfil) {
    $stmt = $this->conn->prepare(
     'UPDATE adotante SET ' . implode(', ', $sets) . ' WHERE id = :id'
    );
    $stmt->execute($params);
   }

   if ($temEmail) {
    $this->atualizarEmailLogin($loginId, $d['email']);
   }

   $this->conn->commit();
  } catch (\Throwable $e) {
   if ($this->conn->inTransaction()) {
    $this->conn->rollBack();
   }
   throw $e;
  }

  return $this->perfilAdotante($adotanteId);
 }

    // -------------------------------------------------------------- ong

 /** Perfil da ONG (shape spec) ou null. Reusa montarOng do OngRepository. */
 public function perfilOng($ongId)
 {
  $repo = new OngRepository();
  return $repo->buscarPorId($ongId);
 }

 /** Atualiza campos da ONG (PATCH) + login.email se enviado. */
 public function atualizarOng($ongId, array $d, $loginId)
 {
  $colunasPermitidas = [
   'nome',
   'telefone_1',
   'telefone_2',
   'cep',
   'logradouro',
   'numero',
   'bairro',
   'cidade',
   'estado',
   'complemento',
  ];

  $sets   = [];
  $params = [':id' => (int) $ongId];

  foreach ($colunasPermitidas as $col) {
   if (array_key_exists($col, $d)) {
    $sets[]            = "`{$col}` = :{$col}";
    $params[":{$col}"] = $d[$col];
   }
  }

  // de-para: descricao (app) → bio (banco)
  if (array_key_exists('descricao', $d)) {
   $sets[]          = '`bio` = :bio';
   $params[':bio'] = $d['descricao'];
  }

  // de-para: foto (app) → avatar (banco)
  if (array_key_exists('foto', $d)) {
   $sets[]             = '`avatar` = :avatar';
   $params[':avatar'] = $d['foto'];
  }

  $temPerfil = !empty($sets);
  $temEmail  = array_key_exists('email', $d);

  if (!$temPerfil && !$temEmail) {
   return null;
  }

  $this->conn->beginTransaction();
  try {
   if ($temPerfil) {
    $stmt = $this->conn->prepare(
     'UPDATE ong SET ' . implode(', ', $sets) . ' WHERE id = :id'
    );
    $stmt->execute($params);
   }

   if ($temEmail) {
    $this->atualizarEmailLogin($loginId, $d['email']);
   }

   $this->conn->commit();
  } catch (\Throwable $e) {
   if ($this->conn->inTransaction()) {
    $this->conn->rollBack();
   }
   throw $e;
  }

  return $this->perfilOng($ongId);
 }

    // -------------------------------------------------------------- veterinario

 /** Perfil do veterinário (shape spec) ou null. */
 public function perfilVeterinario($vetId)
 {
  $stmt = $this->conn->prepare(
   "SELECT v.*, l.email AS login_email, l.status AS login_status
             FROM veterinario v
             LEFT JOIN login l ON l.id = v.fk_login_id
             WHERE v.id = :id LIMIT 1"
  );
  $stmt->execute([':id' => (int) $vetId]);
  $row = $stmt->fetch(\PDO::FETCH_ASSOC);

  return $row ? $this->montarVeterinario($row) : null;
 }

 /** Atualiza campos do veterinário (PATCH) + login.email se enviado. */
 public function atualizarVeterinario($vetId, array $d, $loginId)
 {
  $colunasPermitidas = [
   'nome',
   'cpf',
   'crmv',
   'data_nascimento',
   'telefone_2',
   'cep',
   'logradouro',
   'numero',
   'bairro',
   'cidade',
   'estado',
   'complemento',
  ];

  $sets   = [];
  $params = [':id' => (int) $vetId];

  foreach ($colunasPermitidas as $col) {
   if (array_key_exists($col, $d)) {
    $sets[]            = "`{$col}` = :{$col}";
    $params[":{$col}"] = $d[$col];
   }
  }

  // de-para: telefone_1 (app) → telefone (banco)
  if (array_key_exists('telefone_1', $d)) {
   $sets[]               = '`telefone` = :telefone';
   $params[':telefone'] = $d['telefone_1'];
  }

  // de-para: foto (app) → avatar (banco)
  if (array_key_exists('foto', $d)) {
   $sets[]             = '`avatar` = :avatar';
   $params[':avatar'] = $d['foto'];
  }

  $temPerfil = !empty($sets);
  $temEmail  = array_key_exists('email', $d);

  if (!$temPerfil && !$temEmail) {
   return null;
  }

  $this->conn->beginTransaction();
  try {
   if ($temPerfil) {
    $stmt = $this->conn->prepare(
     'UPDATE veterinario SET ' . implode(', ', $sets) . ' WHERE id = :id'
    );
    $stmt->execute($params);
   }

   if ($temEmail) {
    $this->atualizarEmailLogin($loginId, $d['email']);
   }

   $this->conn->commit();
  } catch (\Throwable $e) {
   if ($this->conn->inTransaction()) {
    $this->conn->rollBack();
   }
   throw $e;
  }

  return $this->perfilVeterinario($vetId);
 }

    // -------------------------------------------------------------- login helpers

 /** Busca login por id (array ou null). */
 public function buscarLoginPorId($id)
 {
  $stmt = $this->conn->prepare('SELECT * FROM login WHERE id = :id LIMIT 1');
  $stmt->execute([':id' => (int) $id]);
  $row = $stmt->fetch(\PDO::FETCH_ASSOC);
  return $row ?: null;
 }

 /** Verifica se email já existe em outro login (exclui o próprio). */
 public function emailExisteEmOutroLogin($email, $loginId): bool
 {
  $stmt = $this->conn->prepare(
   'SELECT 1 FROM login WHERE email = :email AND id <> :id LIMIT 1'
  );
  $stmt->execute([
   ':email' => strtolower(trim($email)),
   ':id'    => (int) $loginId,
  ]);
  return (bool) $stmt->fetchColumn();
 }

    // -------------------------------------------------------------- shape helpers

 /** Monta shape do adotante a partir da linha do banco. */
 private function montarAdotante(array $a): array
 {
  return [
   'id'               => (int) $a['id'],
   'nome'             => $a['nome'],
   'cpf'              => $a['cpf'],
   'data_nascimento'  => $a['data_nascimento'],
   'cep'              => $a['cep'],
   'numero'           => $a['numero'] !== null ? (string) $a['numero'] : null,
   'bairro'           => $a['bairro'],
   'cidade'           => $a['cidade'],
   'estado'           => $a['estado'],
   'complemento'      => $a['complemento'],
   'logradouro'       => $a['logradouro'],
   'telefone_1'       => $a['telefone_1'],
   'telefone_2'       => $a['telefone_2'],
   'foto'             => !empty($a['avatar']) ? $a['avatar'] : null,
   'email'            => isset($a['login_email']) ? $a['login_email'] : null,
   'status'           => isset($a['login_status']) ? $a['login_status'] : null,
   'ranking'          => isset($a['ranking']) && $a['ranking'] !== null
    ? $a['ranking']
    : (isset($a['status']) ? $a['status'] : null),
  ];
 }

 /** Monta shape do veterinário a partir da linha do banco. */
 private function montarVeterinario(array $v): array
 {
  return [
   'id'               => (int) $v['id'],
   'nome'             => $v['nome'],
   'cpf'              => $v['cpf'],
   'crmv'             => $v['crmv'],
   'data_nascimento'  => $v['data_nascimento'],
   'telefone_1'       => $v['telefone'],
   'telefone_2'       => $v['telefone_2'],
   'cep'              => $v['cep'],
   'logradouro'       => $v['logradouro'],
   'numero'           => $v['numero'] !== null ? (string) $v['numero'] : null,
   'bairro'           => $v['bairro'],
   'cidade'           => $v['cidade'],
   'estado'           => $v['estado'],
   'complemento'      => $v['complemento'],
   'foto'             => !empty($v['avatar']) ? $v['avatar'] : null,
   'email'            => isset($v['login_email']) ? $v['login_email'] : null,
   'status'           => isset($v['login_status']) ? $v['login_status'] : null,
  ];
 }

 /** Atualiza email no login (validação e unicidade devem ser feitas antes). */
 private function atualizarEmailLogin($loginId, $novoEmail)
 {
  $stmt = $this->conn->prepare('UPDATE login SET email = :email WHERE id = :id');
  $stmt->execute([
   ':email' => strtolower(trim($novoEmail)),
   ':id'    => (int) $loginId,
  ]);
 }

 private function escalarId($sql, $id)
 {
  $stmt = $this->conn->prepare($sql);
  $stmt->execute([':id' => (int) $id]);
  $v = $stmt->fetchColumn();
  return $v === false ? null : (int) $v;
 }
}
