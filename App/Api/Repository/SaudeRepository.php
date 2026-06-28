<?php

namespace App\Api\Repository;

use FW\DB\Connection;

/**
 * Acesso a dados do módulo de Saúde do Animal (seção C.5).
 * Tabelas: vacina (append-only), procedimento (append-only), saude_animal (upsert),
 * historico_animal (auditoria — alimentada a cada vacina/procedimento). Sintaxe PHP 7.0.
 */
class SaudeRepository
{
    /** @var \PDO */
    private $conn;

    /** @var AnimalRepository */
    private $animalRepo;

    public function __construct()
    {
        $this->conn       = (new Connection())->getConn();
        $this->animalRepo = new AnimalRepository();
    }

    // ---------------------------------------------------------------- vacinas

    public function listarVacinas($animalId): array
    {
        $stmt = $this->conn->prepare(
            'SELECT * FROM vacina WHERE fk_animal_id = :id ORDER BY data_aplicacao DESC, id DESC'
        );
        $stmt->execute([':id' => (int) $animalId]);
        return array_map([$this, 'formatarVacina'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /** Insere vacina (append-only) + registra na auditoria, em transação. Retorna o id. */
    public function adicionarVacina($animalId, array $d, array $autor): int
    {
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO vacina
                 (nome, data_aplicacao, data_reforco, veterinario_nome, clinica_nome,
                  fk_animal_id, fk_login_id, criado_em, criado_por)
                 VALUES (:nome, :aplic, :reforco, :vet, :clinica, :animal, :login, NOW(), :por)"
            );
            $stmt->execute([
                ':nome'    => $d['nome'],
                ':aplic'   => $d['data_aplicacao'],
                ':reforco' => isset($d['data_reforco']) && $d['data_reforco'] !== '' ? $d['data_reforco'] : null,
                ':vet'     => isset($d['veterinario_nome']) ? $d['veterinario_nome'] : null,
                ':clinica' => isset($d['clinica_nome']) ? $d['clinica_nome'] : null,
                ':animal'  => (int) $animalId,
                ':login'   => $autor['login_id'],
                ':por'     => $autor['nome'],
            ]);
            $id = (int) $this->conn->lastInsertId();

            $this->registrarAuditoria(
                (int) $animalId,
                'vacinacao',
                'Vacina aplicada: ' . $d['nome'],
                $d['data_aplicacao'],
                $autor
            );

            $this->conn->commit();
            return $id;
        } catch (\Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    // ---------------------------------------------------------------- procedimentos

    public function listarProcedimentos($animalId): array
    {
        $stmt = $this->conn->prepare(
            'SELECT * FROM procedimento WHERE fk_animal_id = :id ORDER BY data DESC, id DESC'
        );
        $stmt->execute([':id' => (int) $animalId]);
        return array_map([$this, 'formatarProcedimento'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function adicionarProcedimento($animalId, array $d, array $autor): int
    {
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO procedimento
                 (nome, tipo, data, veterinario_nome, observacoes, anexo_url,
                  fk_animal_id, fk_login_id, criado_em, criado_por)
                 VALUES (:nome, :tipo, :data, :vet, :obs, :anexo, :animal, :login, NOW(), :por)"
            );
            $stmt->execute([
                ':nome'   => $d['nome'],
                ':tipo'   => $d['tipo'],
                ':data'   => $d['data'],
                ':vet'    => isset($d['veterinario_nome']) ? $d['veterinario_nome'] : null,
                ':obs'    => isset($d['observacoes']) ? $d['observacoes'] : null,
                ':anexo'  => isset($d['anexo_url']) ? $d['anexo_url'] : null,
                ':animal' => (int) $animalId,
                ':login'  => $autor['login_id'],
                ':por'    => $autor['nome'],
            ]);
            $id = (int) $this->conn->lastInsertId();

            $this->registrarAuditoria(
                (int) $animalId,
                'procedimento',
                ucfirst($d['tipo']) . ': ' . $d['nome'],
                $d['data'],
                $autor
            );

            $this->conn->commit();
            return $id;
        } catch (\Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    // ---------------------------------------------------------------- saude_animal

    /** Saúde do animal (shape spec). Se não houver linha, devolve defaults. */
    public function saude($animalId): array
    {
        $stmt = $this->conn->prepare('SELECT * FROM saude_animal WHERE fk_animal_id = :id LIMIT 1');
        $stmt->execute([':id' => (int) $animalId]);
        $r = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$r) {
            return [
                'fk_animal_id'           => (int) $animalId,
                'apto_para_adocao'       => true,
                'temperamento'           => null,
                'necessidades_especiais' => null,
                'condicao_geral'         => null,
            ];
        }

        return [
            'fk_animal_id'           => (int) $r['fk_animal_id'],
            'apto_para_adocao'       => (bool) $r['apto_para_adocao'],
            'temperamento'           => $r['temperamento'],
            'necessidades_especiais' => $r['necessidades_especiais'],
            'condicao_geral'         => $r['condicao_geral'],
        ];
    }

    /** Upsert da saúde (atualiza só os campos presentes em $d). */
    public function salvarSaude($animalId, array $d): array
    {
        $existe = $this->conn->prepare('SELECT id FROM saude_animal WHERE fk_animal_id = :id LIMIT 1');
        $existe->execute([':id' => (int) $animalId]);
        $atual = $this->saude($animalId);

        $apto    = array_key_exists('apto_para_adocao', $d) ? (int) (bool) $d['apto_para_adocao'] : (int) $atual['apto_para_adocao'];
        $temp    = array_key_exists('temperamento', $d) ? $d['temperamento'] : $atual['temperamento'];
        $neces   = array_key_exists('necessidades_especiais', $d) ? $d['necessidades_especiais'] : $atual['necessidades_especiais'];
        $cond    = array_key_exists('condicao_geral', $d) ? $d['condicao_geral'] : $atual['condicao_geral'];

        if ($existe->fetchColumn()) {
            $stmt = $this->conn->prepare(
                "UPDATE saude_animal
                 SET apto_para_adocao = :apto, temperamento = :temp,
                     necessidades_especiais = :neces, condicao_geral = :cond, atualizado_em = NOW()
                 WHERE fk_animal_id = :id"
            );
        } else {
            $stmt = $this->conn->prepare(
                "INSERT INTO saude_animal
                 (fk_animal_id, apto_para_adocao, temperamento, necessidades_especiais, condicao_geral, atualizado_em)
                 VALUES (:id, :apto, :temp, :neces, :cond, NOW())"
            );
        }
        $stmt->execute([
            ':id'    => (int) $animalId,
            ':apto'  => $apto,
            ':temp'  => $temp,
            ':neces' => $neces,
            ':cond'  => $cond,
        ]);

        return $this->saude($animalId);
    }

    // ---------------------------------------------------------------- carteira

    /**
     * Dados da carteira de vacinação/saúde. qr_code_url/pdf_url são STUB (null) —
     * geração via API pendente (mesma decisão do termo). Retorna null se o animal não existe.
     */
    public function carteira($animalId)
    {
        $animal = $this->animalRepo->buscarPorId((int) $animalId);
        if (!$animal) {
            return null;
        }

        return [
            'animal_id'       => $animal['id'],
            'nome'            => $animal['nome'],
            'especie'         => $animal['especie'],
            'raca'            => $animal['raca'],
            'data_nascimento' => $animal['data_nascimento'],
            'castrado'        => $animal['castrado'],
            'alergias'        => $animal['alergias'],
            'foto'            => $animal['foto'],
            'qr_code_url'     => null, // TODO(QR): gerar via API
            'pdf_url'         => null, // TODO(PDF): gerar via TCPDF
        ];
    }

    // ---------------------------------------------------------------- /vet/atendimentos

    /** Animais atendidos por um veterinário (via fk_login_id em vacina/procedimento). */
    public function atendimentosDoVet($loginId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT a.id AS animal_id, a.nome AS animal_nome, a.foto AS animal_foto,
                    e.nome AS animal_especie,
                    (SELECT MAX(v.data_aplicacao) FROM vacina v WHERE v.fk_animal_id = a.id AND v.fk_login_id = :login1) AS ultima_vacina,
                    (SELECT MAX(p.data) FROM procedimento p WHERE p.fk_animal_id = a.id AND p.fk_login_id = :login2) AS ultimo_procedimento
             FROM animal a
             LEFT JOIN especie e ON e.id = a.fk_especie_id
             WHERE a.id IN (
                 SELECT fk_animal_id FROM vacina WHERE fk_login_id = :login3
                 UNION
                 SELECT fk_animal_id FROM procedimento WHERE fk_login_id = :login4
             )
             ORDER BY a.nome"
        );
        $stmt->execute([
            ':login1' => (int) $loginId,
            ':login2' => (int) $loginId,
            ':login3' => (int) $loginId,
            ':login4' => (int) $loginId,
        ]);

        return array_map(function ($r) {
            return [
                'animal_id'          => (int) $r['animal_id'],
                'animal_nome'        => $r['animal_nome'],
                'animal_especie'     => $r['animal_especie'],
                'animal_foto'        => !empty($r['animal_foto']) ? $r['animal_foto'] : null,
                'ultima_vacina'      => $r['ultima_vacina'],
                'ultimo_procedimento'=> $r['ultimo_procedimento'],
            ];
        }, $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    // ---------------------------------------------------------------- autor / auditoria

    /**
     * Resolve o autor (vet/ong) a partir do login: nome + id do perfil.
     * Usado para criado_por e para os FKs da auditoria.
     */
    public function resolverAutor($loginId, $tipoUsuario): array
    {
        $autor = ['login_id' => (int) $loginId, 'tipo' => $tipoUsuario, 'perfil_id' => null, 'nome' => null];

        if ($tipoUsuario === 'veterinario') {
            $row = $this->umaLinha('SELECT id, nome FROM veterinario WHERE fk_login_id = :id LIMIT 1', $loginId);
        } elseif ($tipoUsuario === 'ong') {
            $row = $this->umaLinha('SELECT id, nome FROM ong WHERE fk_login_id = :id LIMIT 1', $loginId);
        } else {
            $row = null;
        }

        if ($row) {
            $autor['perfil_id'] = (int) $row['id'];
            $autor['nome']      = $row['nome'];
        }
        if (empty($autor['nome'])) {
            $autor['nome'] = $tipoUsuario === 'ong' ? 'ONG' : 'Veterinário';
        }
        return $autor;
    }

    private function registrarAuditoria($animalId, $tipo, $descricao, $data, array $autor)
    {
        $fkVet = $autor['tipo'] === 'veterinario' ? $autor['perfil_id'] : null;
        $fkOng = $autor['tipo'] === 'ong' ? $autor['perfil_id'] : null;

        $stmt = $this->conn->prepare(
            "INSERT INTO historico_animal
             (descricao, data, tipo, fk_animal_id, fk_ong_id, fk_veterinario_id, autor_nome)
             VALUES (:desc, :data, :tipo, :animal, :ong, :vet, :autor)"
        );
        $stmt->execute([
            ':desc'   => $descricao,
            ':data'   => $data,
            ':tipo'   => $tipo,
            ':animal' => (int) $animalId,
            ':ong'    => $fkOng,
            ':vet'    => $fkVet,
            ':autor'  => $autor['nome'],
        ]);
    }

    // ---------------------------------------------------------------- helpers

    public function animalExiste($animalId): bool
    {
        return $this->animalRepo->existe((int) $animalId);
    }

    private function formatarVacina(array $r): array
    {
        return [
            'id'               => (int) $r['id'],
            'nome'             => $r['nome'],
            'data_aplicacao'   => $r['data_aplicacao'],
            'data_reforco'     => $r['data_reforco'],
            'veterinario_nome' => $r['veterinario_nome'],
            'clinica_nome'     => $r['clinica_nome'],
            'criado_em'        => $r['criado_em'],
            'criado_por'       => $r['criado_por'],
        ];
    }

    private function formatarProcedimento(array $r): array
    {
        return [
            'id'               => (int) $r['id'],
            'nome'             => $r['nome'],
            'tipo'             => $r['tipo'],
            'data'             => $r['data'],
            'veterinario_nome' => $r['veterinario_nome'],
            'observacoes'      => $r['observacoes'],
            'anexo_url'        => $r['anexo_url'],
            'criado_em'        => $r['criado_em'],
            'criado_por'       => $r['criado_por'],
        ];
    }

    private function umaLinha($sql, $id)
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => (int) $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
