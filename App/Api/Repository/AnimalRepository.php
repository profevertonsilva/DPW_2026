<?php

namespace App\Api\Repository;

use FW\DB\Connection;

/**
 * Acesso a dados do módulo de Animais (tabelas: animal, especie, raca, animal_raca,
 * animal_imagens, ong_animal, historico_animal).
 *
 * O shape de saída segue EXATAMENTE o "Shape Animal" da especificação (seção C.2):
 * `especie` e `raca` são strings (nomes), `foto` é a capa única e `fotos[]` são as
 * imagens extras, `idade_anos` é calculado e `ong` é {id, nome}. Sintaxe PHP 7.0.
 */
class AnimalRepository
{
    /** @var \PDO */
    private $conn;

    public function __construct()
    {
        $this->conn = (new Connection())->getConn();
    }

    /**
     * Lista animais com filtros opcionais. Retorna ARRAY PURO de Animal (spec).
     * Sem filtro de status explícito, retorna apenas os disponíveis.
     */
    public function listar(array $filtros = []): array
    {
        $where  = [];
        $params = [];

        if (empty($filtros['status'])) {
            $where[]           = 'a.status = :status';
            $params[':status'] = 'disponivel';
        }

        if (!empty($filtros['especie'])) {
            $where[]               = 'a.fk_especie_id = :especie_id';
            $params[':especie_id'] = (int) $filtros['especie'];
        }

        if (!empty($filtros['porte'])) {
            $where[]          = 'a.porte = :porte';
            $params[':porte'] = $filtros['porte'];
        }

        if (!empty($filtros['sexo'])) {
            $where[]         = 'a.sexo = :sexo';
            $params[':sexo'] = $filtros['sexo'];
        }

        if (!empty($filtros['localizacao'])) {
            $like                = '%' . $filtros['localizacao'] . '%';
            $where[]             = '(a.localizacao LIKE :loc OR a.local_cidade LIKE :loc2)';
            $params[':loc']      = $like;
            $params[':loc2']     = $like;
        }

        if (!empty($filtros['busca'])) {
            $like              = '%' . $filtros['busca'] . '%';
            $where[]           = '(a.nome LIKE :busca OR a.descricao LIKE :busca2)';
            $params[':busca']  = $like;
            $params[':busca2'] = $like;
        }

        $condicao = $where ? implode(' AND ', $where) : '1=1';

        $stmt = $this->conn->prepare(
            "SELECT a.*, e.nome AS especie_nome
             FROM animal a
             LEFT JOIN especie e ON e.id = a.fk_especie_id
             WHERE {$condicao}
             ORDER BY a.id DESC"
        );
        $stmt->execute($params);

        return array_map([$this, 'montarAnimal'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /** Retorna o Animal completo (shape spec) ou null se não existir. */
    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT a.*, e.nome AS especie_nome
             FROM animal a
             LEFT JOIN especie e ON e.id = a.fk_especie_id
             WHERE a.id = :id LIMIT 1"
        );
        $stmt->execute([':id' => (int) $id]);
        $a = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $a ? $this->montarAnimal($a) : null;
    }

    /** Existe um animal com este id? (checagem leve, sem montar o shape). */
    public function existe($id): bool
    {
        $stmt = $this->conn->prepare('SELECT 1 FROM animal WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => (int) $id]);
        return (bool) $stmt->fetchColumn();
    }

    /** Animais de uma ONG. Retorna ARRAY PURO de Animal (spec). */
    public function listarPorOng($ongId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT a.*, e.nome AS especie_nome
             FROM animal a
             INNER JOIN ong_animal oa ON oa.fk_animal_id = a.id
             LEFT JOIN especie e ON e.id = a.fk_especie_id
             WHERE oa.fk_ong_id = :ong_id
             ORDER BY a.id DESC"
        );
        $stmt->execute([':ong_id' => (int) $ongId]);

        return array_map([$this, 'montarAnimal'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * Cria um animal e o vincula à ONG (transacional).
     * Opcionalmente vincula raças (array de IDs em $d['racas']).
     */
    public function criar(array $d, $ongId): int
    {
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO animal
                 (nome, data_nascimento, sexo, fk_especie_id, cor, castrado, data_castracao,
                  descricao, historico_resgate, alergias, porte, localizacao, local_cep,
                  local_logradouro, local_numero, local_bairro, local_cidade, local_estado, foto, status)
                 VALUES
                 (:nome, :data_nascimento, :sexo, :fk_especie_id, :cor, :castrado, :data_castracao,
                  :descricao, :historico_resgate, :alergias, :porte, :localizacao, :local_cep,
                  :local_logradouro, :local_numero, :local_bairro, :local_cidade, :local_estado, :foto, 'disponivel')"
            );
            $stmt->execute([
                ':nome'              => $d['nome'],
                ':data_nascimento'   => isset($d['data_nascimento']) ? $d['data_nascimento'] : null,
                ':sexo'              => $d['sexo'],
                ':fk_especie_id'     => (int) $d['fk_especie_id'],
                ':cor'               => isset($d['cor']) ? $d['cor'] : null,
                ':castrado'          => isset($d['castrado']) ? (int) (bool) $d['castrado'] : 0,
                ':data_castracao'    => isset($d['data_castracao']) ? $d['data_castracao'] : null,
                ':descricao'         => isset($d['descricao']) ? $d['descricao'] : null,
                ':historico_resgate' => isset($d['historico_resgate']) ? $d['historico_resgate'] : null,
                ':alergias'          => isset($d['alergias']) ? $d['alergias'] : null,
                ':porte'             => $d['porte'],
                ':localizacao'       => isset($d['localizacao']) ? $d['localizacao'] : null,
                ':local_cep'         => isset($d['local_cep']) ? $d['local_cep'] : null,
                ':local_logradouro'  => isset($d['local_logradouro']) ? $d['local_logradouro'] : null,
                ':local_numero'      => isset($d['local_numero']) ? $d['local_numero'] : null,
                ':local_bairro'      => isset($d['local_bairro']) ? $d['local_bairro'] : null,
                ':local_cidade'      => isset($d['local_cidade']) ? $d['local_cidade'] : null,
                ':local_estado'      => isset($d['local_estado']) ? $d['local_estado'] : null,
                ':foto'              => isset($d['foto']) ? $d['foto'] : null,
            ]);

            $animalId = (int) $this->conn->lastInsertId();

            $stmtOng = $this->conn->prepare(
                'INSERT INTO ong_animal (fk_ong_id, fk_animal_id) VALUES (:ong_id, :animal_id)'
            );
            $stmtOng->execute([':ong_id' => (int) $ongId, ':animal_id' => $animalId]);

            $this->vincularRacas($animalId, isset($d['racas']) ? $d['racas'] : null);

            $this->conn->commit();
            return $animalId;
        } catch (\Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Atualiza apenas os campos presentes em $d (PATCH semântico).
     * Se $d['racas'] for um array, substitui todos os vínculos de raça.
     */
    public function atualizar($animalId, array $d): bool
    {
        $colunasPermitidas = [
            'nome', 'data_nascimento', 'sexo', 'fk_especie_id', 'cor', 'castrado',
            'data_castracao', 'descricao', 'historico_resgate', 'alergias', 'porte',
            'localizacao', 'local_cep', 'local_logradouro', 'local_numero',
            'local_bairro', 'local_cidade', 'local_estado', 'foto', 'status',
        ];

        $sets   = [];
        $params = [':id' => (int) $animalId];

        foreach ($colunasPermitidas as $col) {
            if (array_key_exists($col, $d)) {
                $sets[]            = "`{$col}` = :{$col}";
                $params[":{$col}"] = $col === 'castrado' ? (int) (bool) $d[$col] : $d[$col];
            }
        }

        $temCampos = !empty($sets);
        $temRacas  = array_key_exists('racas', $d) && is_array($d['racas']);

        if (!$temCampos && !$temRacas) {
            return false;
        }

        $this->conn->beginTransaction();
        try {
            if ($temCampos) {
                $stmt = $this->conn->prepare(
                    'UPDATE animal SET ' . implode(', ', $sets) . ' WHERE id = :id'
                );
                $stmt->execute($params);
            }

            if ($temRacas) {
                $stmtDel = $this->conn->prepare('DELETE FROM animal_raca WHERE fk_animal_id = :id');
                $stmtDel->execute([':id' => (int) $animalId]);
                $this->vincularRacas((int) $animalId, $d['racas']);
            }

            $this->conn->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Histórico/auditoria do animal (append-only). Shape spec C.5:
     * { id, tipo, descricao, data, autor_nome, fk_animal_id }.
     */
    public function historico($animalId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT id, tipo, descricao, data, autor_nome, fk_animal_id
             FROM historico_animal
             WHERE fk_animal_id = :id
             ORDER BY data DESC, id DESC"
        );
        $stmt->execute([':id' => (int) $animalId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($r) {
            return [
                'id'           => (int) $r['id'],
                'tipo'         => $r['tipo'],
                'descricao'    => $r['descricao'],
                'data'         => $r['data'],
                'autor_nome'   => $r['autor_nome'],
                'fk_animal_id' => (int) $r['fk_animal_id'],
            ];
        }, $rows);
    }

    /**
     * id da ONG a partir do login_id.
     * NOTA: a spec sugere o vínculo `login.fk_ong_id`, mas o grupo decidiu usar
     * `ong.fk_login_id` (simétrico a adotante/veterinário). Ver memória do projeto.
     */
    public function ongIdPorLoginId($loginId)
    {
        $stmt = $this->conn->prepare('SELECT id FROM ong WHERE fk_login_id = :id LIMIT 1');
        $stmt->execute([':id' => (int) $loginId]);
        $v = $stmt->fetchColumn();
        return $v === false ? null : (int) $v;
    }

    public function animalPertenceAOng($animalId, $ongId): bool
    {
        $stmt = $this->conn->prepare(
            'SELECT 1 FROM ong_animal WHERE fk_animal_id = :animal_id AND fk_ong_id = :ong_id LIMIT 1'
        );
        $stmt->execute([':animal_id' => (int) $animalId, ':ong_id' => (int) $ongId]);
        return (bool) $stmt->fetchColumn();
    }

    /** Espécies — shape spec: [{ id, nome }]. */
    public function especies(): array
    {
        $stmt = $this->conn->query('SELECT id, nome FROM especie ORDER BY nome');
        return array_map(function ($r) {
            return ['id' => (int) $r['id'], 'nome' => $r['nome']];
        }, $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /** Raças — shape spec: [{ id, nome, fk_especie_id }]. */
    public function racas($especieId = null): array
    {
        if ($especieId) {
            $stmt = $this->conn->prepare(
                'SELECT id, nome, fk_especie_id FROM raca WHERE fk_especie_id = :eid ORDER BY nome'
            );
            $stmt->execute([':eid' => (int) $especieId]);
        } else {
            $stmt = $this->conn->query('SELECT id, nome, fk_especie_id FROM raca ORDER BY nome');
        }
        return array_map(function ($r) {
            return [
                'id'            => (int) $r['id'],
                'nome'          => $r['nome'],
                'fk_especie_id' => $r['fk_especie_id'] !== null ? (int) $r['fk_especie_id'] : null,
            ];
        }, $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    // ----------------------------------------------------------------- helpers

    /** Monta o "Shape Animal" exato da especificação a partir de uma linha de `animal`. */
    private function montarAnimal(array $a): array
    {
        $id = (int) $a['id'];

        return [
            'id'                => $id,
            'nome'              => $a['nome'],
            'data_nascimento'   => $a['data_nascimento'],
            'sexo'              => $a['sexo'],
            'especie'           => isset($a['especie_nome']) ? $a['especie_nome'] : null,
            'raca'              => $this->racaPrincipal($id),
            'cor'               => $a['cor'],
            'porte'             => $a['porte'],
            'castrado'          => (bool) $a['castrado'],
            'data_castracao'    => $a['data_castracao'],
            'descricao'         => $a['descricao'],
            'historico_resgate' => $a['historico_resgate'],
            'alergias'          => $a['alergias'],
            'localizacao'       => $a['localizacao'],
            'status'            => $a['status'],
            'foto'              => !empty($a['foto']) ? $a['foto'] : null,
            'fotos'             => $this->fotosDoAnimal($id),
            'idade_anos'        => $this->idadeAnos($a['data_nascimento']),
            'local_cep'         => $a['local_cep'],
            'local_logradouro'  => $a['local_logradouro'],
            'local_numero'      => $a['local_numero'],
            'local_bairro'      => $a['local_bairro'],
            'local_cidade'      => $a['local_cidade'],
            'local_estado'      => $a['local_estado'],
            'ong'               => $this->ongDoAnimal($id),
        ];
    }

    /** 1ª raça do animal (N:N), como string ou null — conforme shape spec. */
    private function racaPrincipal($animalId)
    {
        $stmt = $this->conn->prepare(
            "SELECT r.nome FROM raca r
             INNER JOIN animal_raca ar ON ar.fk_raca_id = r.id
             WHERE ar.fk_animal_id = :id
             ORDER BY ar.id ASC LIMIT 1"
        );
        $stmt->execute([':id' => (int) $animalId]);
        $v = $stmt->fetchColumn();
        return $v === false ? null : $v;
    }

    /** URLs das imagens extras (animal_imagens) ordenadas por `ordem`. */
    private function fotosDoAnimal($animalId): array
    {
        $stmt = $this->conn->prepare(
            'SELECT caminho_imagem FROM animal_imagens WHERE fk_animal_id = :id ORDER BY ordem'
        );
        $stmt->execute([':id' => (int) $animalId]);
        return array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'caminho_imagem');
    }

    /** ONG do animal (1ª via ong_animal), como {id, nome} ou null. */
    private function ongDoAnimal($animalId)
    {
        $stmt = $this->conn->prepare(
            "SELECT o.id, o.nome FROM ong o
             INNER JOIN ong_animal oa ON oa.fk_ong_id = o.id
             WHERE oa.fk_animal_id = :id
             ORDER BY oa.id ASC LIMIT 1"
        );
        $stmt->execute([':id' => (int) $animalId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? ['id' => (int) $row['id'], 'nome' => $row['nome']] : null;
    }

    /** Idade em anos completos a partir da data de nascimento (ou null). */
    private function idadeAnos($dataNascimento)
    {
        if (empty($dataNascimento)) {
            return null;
        }
        try {
            $nasc = new \DateTime($dataNascimento);
            $hoje = new \DateTime('today');
            return (int) $nasc->diff($hoje)->y;
        } catch (\Exception $e) {
            return null;
        }
    }

    /** Insere vínculos animal_raca a partir de um array de IDs (ignora vazios). */
    private function vincularRacas($animalId, $racas)
    {
        if (empty($racas) || !is_array($racas)) {
            return;
        }
        $stmt = $this->conn->prepare(
            'INSERT INTO animal_raca (fk_raca_id, fk_animal_id) VALUES (:raca_id, :animal_id)'
        );
        foreach ($racas as $racaId) {
            $stmt->execute([':raca_id' => (int) $racaId, ':animal_id' => (int) $animalId]);
        }
    }
}
