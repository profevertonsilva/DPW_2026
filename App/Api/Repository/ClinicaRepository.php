<?php

namespace App\Api\Repository;

use FW\DB\Connection;

/**
 * Acesso a dados do módulo de Clínicas Veterinárias (seção C.12).
 * Tabelas: clinica, vet_clinica, veterinario. Sintaxe PHP 7.0.
 *
 * De-para (spec): clinica.avatar → foto, clinica.email → email (ambos opcionais).
 */
class ClinicaRepository
{
 /** @var \PDO */
 private $conn;

 public function __construct()
 {
  $this->conn = (new Connection())->getConn();
 }

 /** Todas as clínicas (array puro de Clinica). */
 public function listar()
 {
  $stmt = $this->conn->query('SELECT c.* FROM clinica c ORDER BY c.nome');
  return array_map([$this, 'montarClinica'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
 }

 /** Uma clínica (shape spec) ou null. */
 public function buscarPorId($id)
 {
  $stmt = $this->conn->prepare('SELECT c.* FROM clinica c WHERE c.id = :id LIMIT 1');
  $stmt->execute([':id' => (int) $id]);
  $row = $stmt->fetch(\PDO::FETCH_ASSOC);

  return $row ? $this->montarClinica($row) : null;
 }

 /** Verifica se a clínica existe. */
 public function existe($id)
 {
  $stmt = $this->conn->prepare('SELECT 1 FROM clinica WHERE id = :id LIMIT 1');
  $stmt->execute([':id' => (int) $id]);
  return (bool) $stmt->fetchColumn();
 }

 /** Cria uma clínica e retorna o id inserido. */
 public function criar(array $d)
 {
  $sql = 'INSERT INTO clinica (nome, cnpj, cep, telefone_1, telefone_2,
                    logradouro, numero, bairro, cidade, estado, complemento)
                VALUES (:nome, :cnpj, :cep, :telefone_1, :telefone_2,
                    :logradouro, :numero, :bairro, :cidade, :estado, :complemento)';
  $stmt = $this->conn->prepare($sql);
  $stmt->execute([
   ':nome'        => $d['nome'],
   ':cnpj'        => isset($d['cnpj']) ? $d['cnpj'] : null,
   ':cep'         => isset($d['cep']) ? $d['cep'] : null,
   ':telefone_1'  => isset($d['telefone_1']) ? $d['telefone_1'] : null,
   ':telefone_2'  => isset($d['telefone_2']) ? $d['telefone_2'] : null,
   ':logradouro'  => isset($d['logradouro']) ? $d['logradouro'] : null,
   ':numero'      => isset($d['numero']) ? (int) $d['numero'] : null,
   ':bairro'      => isset($d['bairro']) ? $d['bairro'] : null,
   ':cidade'      => isset($d['cidade']) ? $d['cidade'] : null,
   ':estado'      => isset($d['estado']) ? $d['estado'] : null,
   ':complemento' => isset($d['complemento']) ? $d['complemento'] : null,
  ]);
  return (int) $this->conn->lastInsertId();
 }

 /** Clínicas associadas a um veterinário (via vet_clinica). */
 public function doVeterinario($vetId)
 {
  $stmt = $this->conn->prepare(
   'SELECT c.* FROM clinica c
             INNER JOIN vet_clinica vc ON vc.fk_clinica_id = c.id
             WHERE vc.fk_veterinario_id = :vetId
             ORDER BY c.nome'
  );
  $stmt->execute([':vetId' => (int) $vetId]);
  return array_map([$this, 'montarClinica'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
 }

 /** Verifica se o vínculo vet-clínica já existe. */
 public function vinculoExiste($vetId, $clinicaId)
 {
  $stmt = $this->conn->prepare(
   'SELECT 1 FROM vet_clinica
             WHERE fk_veterinario_id = :vetId AND fk_clinica_id = :clinicaId LIMIT 1'
  );
  $stmt->execute([':vetId' => (int) $vetId, ':clinicaId' => (int) $clinicaId]);
  return (bool) $stmt->fetchColumn();
 }

 /** Associa veterinário a clínica. */
 public function associar($vetId, $clinicaId)
 {
  $stmt = $this->conn->prepare(
   'INSERT INTO vet_clinica (fk_veterinario_id, fk_clinica_id)
             VALUES (:vetId, :clinicaId)'
  );
  $stmt->execute([':vetId' => (int) $vetId, ':clinicaId' => (int) $clinicaId]);
 }

 /** Desassocia veterinário de clínica. */
 public function desassociar($vetId, $clinicaId)
 {
  $stmt = $this->conn->prepare(
   'DELETE FROM vet_clinica
             WHERE fk_veterinario_id = :vetId AND fk_clinica_id = :clinicaId'
  );
  $stmt->execute([':vetId' => (int) $vetId, ':clinicaId' => (int) $clinicaId]);
 }

 /**
  * Resolve o veterinario.id a partir do login.id (fk_login_id).
  * Retorna o id (int) ou null se o perfil não existir.
  */
 public function vetIdPorLogin($loginId)
 {
  $stmt = $this->conn->prepare(
   'SELECT id FROM veterinario WHERE fk_login_id = :id LIMIT 1'
  );
  $stmt->execute([':id' => (int) $loginId]);
  $id = $stmt->fetchColumn();
  return $id ? (int) $id : null;
 }

    // ----------------------------------------------------------------- helpers

 /** Monta o shape de Clinica a partir da linha (com de-para de colunas). */
 private function montarClinica(array $c)
 {
  return [
   'id'          => (int) $c['id'],
   'nome'        => $c['nome'],
   'cnpj'        => isset($c['cnpj']) ? $c['cnpj'] : null,
   'email'       => isset($c['email']) ? $c['email'] : null,
   'telefone_1'  => $c['telefone_1'],
   'telefone_2'  => $c['telefone_2'],
   'cep'         => $c['cep'],
   'logradouro'  => $c['logradouro'],
   'numero'      => $c['numero'] !== null ? (string) $c['numero'] : null,
   'bairro'      => $c['bairro'],
   'cidade'      => $c['cidade'],
   'estado'      => $c['estado'],
   'complemento' => $c['complemento'],
   'foto'        => !empty($c['avatar']) ? $c['avatar'] : null,
  ];
 }
}
