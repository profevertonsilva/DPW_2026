<?php

namespace App\Api\Repository;

use FW\DB\Connection;

/**
 * Acesso a dados de autenticação/cadastro (login + perfis).
 *
 * Diferente dos DAOs web (que em erro fazem redirect HTML), aqui as exceções
 * de PDO sobem para o ApiRouter, que as converte no padrão JSON de erro.
 * Inserts de cadastro são transacionais (login + perfil juntos).
 * Sintaxe compatível com PHP 7.0.
 */
class AuthRepository
{
    /** @var \PDO */
    private $conn;

    public function __construct()
    {
        $this->conn = (new Connection())->getConn();
    }

    public function buscarLoginPorEmail($email)
    {
        $stmt = $this->conn->prepare('SELECT * FROM login WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => strtolower(trim($email))]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function buscarLoginPorId($id)
    {
        $stmt = $this->conn->prepare('SELECT * FROM login WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => (int) $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function emailExiste($email): bool
    {
        $stmt = $this->conn->prepare('SELECT 1 FROM login WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => strtolower(trim($email))]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Existência de um valor numa coluna. tabela/coluna são valores internos
     * (nunca vêm do cliente), portanto a interpolação é segura.
     */
    public function valorExiste($tabela, $coluna, $valor): bool
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM `{$tabela}` WHERE `{$coluna}` = :v LIMIT 1");
        $stmt->execute([':v' => $valor]);
        return (bool) $stmt->fetchColumn();
    }

    /** Nome exibível do usuário, buscado na tabela de perfil conforme o tipo. */
    public function nomeDoPerfil(array $login)
    {
        $tipo = isset($login['tipo_usuario']) ? $login['tipo_usuario'] : null;
        $id   = isset($login['id']) ? (int) $login['id'] : 0;

        switch ($tipo) {
            case 'adotante':
                return $this->escalar('SELECT nome FROM adotante WHERE fk_login_id = :id LIMIT 1', $id);
            case 'veterinario':
                return $this->escalar('SELECT nome FROM veterinario WHERE fk_login_id = :id LIMIT 1', $id);
            case 'ong':
                return $this->escalar('SELECT nome FROM ong WHERE fk_login_id = :id LIMIT 1', $id);
            case 'administrador':
                // administrador ainda não tem fk_login_id (ver pontos em aberto da spec).
                return null;
            default:
                return null;
        }
    }

    public function atualizarSenha($loginId, $senhaPlana)
    {
        $stmt = $this->conn->prepare('UPDATE login SET senha = :senha WHERE id = :id');
        $stmt->execute([
            ':senha' => password_hash($senhaPlana, PASSWORD_DEFAULT),
            ':id'    => (int) $loginId,
        ]);
    }

    public function criarAdotante(array $d): array
    {
        return $this->criarComLogin('adotante', $d, function ($conn, $loginId) use ($d) {
            $sql = 'INSERT INTO adotante
                (nome, cpf, data_nascimento, cep, numero, bairro, cidade, estado,
                 complemento, logradouro, telefone_1, telefone_2, status, fk_login_id)
                VALUES
                (:nome, :cpf, :data_nascimento, :cep, :numero, :bairro, :cidade, :estado,
                 :complemento, :logradouro, :telefone_1, :telefone_2, :status, :fk_login_id)';
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nome'            => $d['nome'],
                ':cpf'             => $d['cpf'],
                ':data_nascimento' => $d['data_nascimento'],
                ':cep'             => $d['cep'],
                ':numero'          => $d['numero'],
                ':bairro'          => $d['bairro'],
                ':cidade'          => $d['cidade'],
                ':estado'          => $d['estado'],
                ':complemento'     => $d['complemento'],
                ':logradouro'      => $d['logradouro'],
                ':telefone_1'      => $d['telefone_1'],
                ':telefone_2'      => $d['telefone_2'],
                ':status'          => 'bom',
                ':fk_login_id'     => $loginId,
            ]);
            return (int) $conn->lastInsertId();
        });
    }

    public function criarOng(array $d): array
    {
        return $this->criarComLogin('ong', $d, function ($conn, $loginId) use ($d) {
            $sql = 'INSERT INTO ong
                (nome, cnpj, status, quantidade_animais, telefone_1, telefone_2, cep,
                 logradouro, numero, bairro, cidade, estado, complemento, fk_login_id)
                VALUES
                (:nome, :cnpj, :status, :quantidade_animais, :telefone_1, :telefone_2, :cep,
                 :logradouro, :numero, :bairro, :cidade, :estado, :complemento, :fk_login_id)';
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nome'               => $d['nome'],
                ':cnpj'               => $d['cnpj'],
                ':status'             => 'a',
                ':quantidade_animais' => 0,
                ':telefone_1'         => $d['telefone_1'],
                ':telefone_2'         => $d['telefone_2'],
                ':cep'                => $d['cep'],
                ':logradouro'         => $d['logradouro'],
                ':numero'             => $d['numero'],
                ':bairro'             => $d['bairro'],
                ':cidade'             => $d['cidade'],
                ':estado'             => $d['estado'],
                ':complemento'        => $d['complemento'],
                ':fk_login_id'        => $loginId,
            ]);
            return (int) $conn->lastInsertId();
        });
    }

    public function criarVeterinario(array $d): array
    {
        return $this->criarComLogin('veterinario', $d, function ($conn, $loginId) use ($d) {
            // de-para: telefone_1 (app) -> veterinario.telefone (banco)
            $sql = 'INSERT INTO veterinario
                (nome, cpf, crmv, data_nascimento, telefone, telefone_2, cep,
                 logradouro, numero, bairro, cidade, estado, complemento, fk_login_id)
                VALUES
                (:nome, :cpf, :crmv, :data_nascimento, :telefone, :telefone_2, :cep,
                 :logradouro, :numero, :bairro, :cidade, :estado, :complemento, :fk_login_id)';
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nome'            => $d['nome'],
                ':cpf'             => $d['cpf'],
                ':crmv'            => $d['crmv'],
                ':data_nascimento' => $d['data_nascimento'],
                ':telefone'        => $d['telefone_1'],
                ':telefone_2'      => $d['telefone_2'],
                ':cep'             => $d['cep'],
                ':logradouro'      => $d['logradouro'],
                ':numero'          => $d['numero'],
                ':bairro'          => $d['bairro'],
                ':cidade'          => $d['cidade'],
                ':estado'          => $d['estado'],
                ':complemento'     => $d['complemento'],
                ':fk_login_id'     => $loginId,
            ]);
            return (int) $conn->lastInsertId();
        });
    }

    /**
     * Cria a linha de login e, na mesma transação, a linha de perfil
     * (via callback que recebe a conexão e o id do login criado).
     */
    private function criarComLogin($tipo, array $d, callable $inserirPerfil): array
    {
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO login (email, senha, status, tipo_usuario)
                 VALUES (:email, :senha, 'a', :tipo)"
            );
            $stmt->execute([
                ':email' => strtolower(trim($d['email'])),
                ':senha' => password_hash($d['senha'], PASSWORD_DEFAULT),
                ':tipo'  => $tipo,
            ]);
            $loginId  = (int) $this->conn->lastInsertId();
            $perfilId = $inserirPerfil($this->conn, $loginId);

            $this->conn->commit();
            return ['login_id' => $loginId, 'perfil_id' => $perfilId];
        } catch (\Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    private function escalar($sql, $id)
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => (int) $id]);
        $valor = $stmt->fetchColumn();
        return $valor === false ? null : $valor;
    }
}
