<?php

namespace App\Api\Repository;

use FW\DB\Connection;

/**
 * Acesso a dados para busca de usuários (seção C.13).
 *
 * Busca por nome (resolvido via COALESCE entre perfis adotante/ong/veterinario)
 * ou email, excluindo administradores e moderadores. Sintaxe PHP 7.0.
 */
class UsuarioRepository
{
    /** @var \PDO */
    private $conn;

    public function __construct()
    {
        $this->conn = (new Connection())->getConn();
    }

    /**
     * Busca usuários por query (nome ou email). Limite 10.
     * Retorna array de shapes { id, nome, tipo_usuario, email }.
     */
    public function buscar($query): array
    {
        $q = trim((string) $query);
        if (mb_strlen($q) < 2) {
            return [];
        }

        $like = '%' . $q . '%';

        $stmt = $this->conn->prepare(
            "SELECT l.id, l.tipo_usuario, l.email,
                    COALESCE(ad.nome, o.nome, v.nome) AS nome
             FROM login l
             LEFT JOIN adotante ad ON ad.fk_login_id = l.id
             LEFT JOIN ong o ON o.fk_login_id = l.id
             LEFT JOIN veterinario v ON v.fk_login_id = l.id
             WHERE l.tipo_usuario NOT IN ('administrador','moderador')
               AND (COALESCE(ad.nome, o.nome, v.nome) LIKE :q OR l.email LIKE :q2)
             ORDER BY nome ASC
             LIMIT 10"
        );
        $stmt->execute([':q' => $like, ':q2' => $like]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map([$this, 'montar'], $rows);
    }

    // ----------------------------------------------------------------- helpers

    /** Monta o shape de resultado da busca. */
    private function montar(array $r): array
    {
        return [
            'id'            => (int) $r['id'],
            'nome'          => $r['nome'] !== null ? $r['nome'] : 'Usuário',
            'tipo_usuario'  => $r['tipo_usuario'],
            'email'         => $r['email'],
        ];
    }
}
