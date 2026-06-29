<?php

namespace App\Api\Repository;

use FW\DB\Connection;

/**
 * Acesso a dados do módulo de ONGs (seção C.3).
 *
 * De-para (spec seção D): `ong.avatar` → `foto`, `ong.bio` → `descricao`,
 * `email`/`status` (conta) vêm do `login` via `ong.fk_login_id`.
 * `quantidade_animais` é contado ao vivo em `ong_animal`. Sintaxe PHP 7.0.
 */
class OngRepository
{
    /** @var \PDO */
    private $conn;

    public function __construct()
    {
        $this->conn = (new Connection())->getConn();
    }

    /** Todas as ONGs (array puro de ONG). */
    public function listar(): array
    {
        $stmt = $this->conn->query(
            "SELECT o.*, l.email AS login_email, l.status AS login_status,
                    (SELECT COUNT(*) FROM ong_animal oa WHERE oa.fk_ong_id = o.id) AS qtd_animais
             FROM ong o
             LEFT JOIN login l ON l.id = o.fk_login_id
             ORDER BY o.nome"
        );
        return array_map([$this, 'montarOng'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /** Uma ONG (shape spec) ou null. */
    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT o.*, l.email AS login_email, l.status AS login_status,
                    (SELECT COUNT(*) FROM ong_animal oa WHERE oa.fk_ong_id = o.id) AS qtd_animais
             FROM ong o
             LEFT JOIN login l ON l.id = o.fk_login_id
             WHERE o.id = :id LIMIT 1"
        );
        $stmt->execute([':id' => (int) $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ? $this->montarOng($row) : null;
    }

    public function existe($id): bool
    {
        $stmt = $this->conn->prepare('SELECT 1 FROM ong WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => (int) $id]);
        return (bool) $stmt->fetchColumn();
    }

    // ----------------------------------------------------------------- helpers

    /** Monta o shape de ONG a partir da linha (com de-para de colunas). */
    private function montarOng(array $o): array
    {
        return [
            'id'                 => (int) $o['id'],
            'nome'               => $o['nome'],
            'cnpj'               => $o['cnpj'],
            'email'              => isset($o['login_email']) ? $o['login_email'] : null,
            'telefone_1'         => $o['telefone_1'],
            'telefone_2'         => $o['telefone_2'],
            'cep'                => $o['cep'],
            'logradouro'         => $o['logradouro'],
            'numero'             => $o['numero'] !== null ? (string) $o['numero'] : null,
            'bairro'             => $o['bairro'],
            'cidade'             => $o['cidade'],
            'estado'             => $o['estado'],
            'complemento'        => $o['complemento'],
            'foto'               => !empty($o['avatar']) ? $o['avatar'] : null,
            'descricao'          => $o['bio'],
            'quantidade_animais' => (int) $o['qtd_animais'],
            'status'             => isset($o['login_status']) ? $o['login_status'] : $o['status'],
        ];
    }
}
