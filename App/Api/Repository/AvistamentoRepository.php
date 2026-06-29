<?php

namespace App\Api\Repository;

use FW\DB\Connection;

/**
 * Acesso a dados de Avistamentos (animal de rua) + Ranking de rastreadores (seção C.6).
 * Tabela: publicacao_encontrado. Sintaxe PHP 7.0.
 *
 * De-para (spec D): data→data_encontro, condicao→condicao_fisica, acoes_tomadas→acoes_realizadas,
 * endereco_texto→localizacao, usuario_id→fk_login_id, lat/lng→latitude/longitude, fotos→JSON.
 * Status no banco usa ESPAÇO ('aguardando acolhimento'); o app usa UNDERSCORE — convertido aqui.
 */
class AvistamentoRepository
{
    const PONTOS_POR_AVISTAMENTO = 10;

    /** @var \PDO */
    private $conn;

    public function __construct()
    {
        $this->conn = (new Connection())->getConn();
    }

    /** Cria um avistamento (status inicial + pontos). Retorna o id. */
    public function criar($loginId, array $d): int
    {
        $fotos = isset($d['fotos']) && is_array($d['fotos']) ? $d['fotos'] : [];
        $foto  = isset($d['foto']) && $d['foto'] !== '' ? $d['foto'] : (count($fotos) ? $fotos[0] : null);

        $stmt = $this->conn->prepare(
            "INSERT INTO publicacao_encontrado
             (fk_animal_id, fk_login_id, data_encontro, localizacao, condicao_fisica, acoes_realizadas,
              status, latitude, longitude, especie, fotos, foto, pontos,
              local_cep, local_logradouro, local_numero, local_bairro, local_cidade, local_estado)
             VALUES
             (NULL, :login, :data, :loc, :cond, :acoes,
              'aguardando acolhimento', :lat, :lng, :especie, :fotos, :foto, :pontos,
              :cep, :logradouro, :numero, :bairro, :cidade, :estado)"
        );
        $stmt->execute([
            ':login'      => (int) $loginId,
            ':data'       => $this->dataParaSql(isset($d['data']) ? $d['data'] : null),
            ':loc'        => isset($d['endereco_texto']) ? $d['endereco_texto'] : '',
            ':cond'       => isset($d['condicao']) ? $d['condicao'] : '',
            ':acoes'      => isset($d['acoes_tomadas']) ? $d['acoes_tomadas'] : null,
            ':lat'        => $this->numOuNull(isset($d['lat']) ? $d['lat'] : null),
            ':lng'        => $this->numOuNull(isset($d['lng']) ? $d['lng'] : null),
            ':especie'    => isset($d['especie']) ? $d['especie'] : null,
            ':fotos'      => json_encode(array_values($fotos), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ':foto'       => $foto,
            ':pontos'     => self::PONTOS_POR_AVISTAMENTO,
            ':cep'        => isset($d['local_cep']) ? $d['local_cep'] : null,
            ':logradouro' => isset($d['local_logradouro']) ? $d['local_logradouro'] : null,
            ':numero'     => isset($d['local_numero']) ? $d['local_numero'] : null,
            ':bairro'     => isset($d['local_bairro']) ? $d['local_bairro'] : null,
            ':cidade'     => isset($d['local_cidade']) ? $d['local_cidade'] : null,
            ':estado'     => isset($d['local_estado']) ? $d['local_estado'] : null,
        ]);
        return (int) $this->conn->lastInsertId();
    }

    /** Lista avistamentos com filtros opcionais (status?, especie?). Array puro. */
    public function listar(array $filtros = []): array
    {
        $where  = [];
        $params = [];

        if (!empty($filtros['status'])) {
            $where[]           = 'status = :status';
            $params[':status'] = $this->statusParaDb($filtros['status']);
        }
        if (!empty($filtros['especie'])) {
            $where[]            = 'especie = :especie';
            $params[':especie'] = $filtros['especie'];
        }

        $cond = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $stmt = $this->conn->prepare("SELECT * FROM publicacao_encontrado {$cond} ORDER BY id DESC");
        $stmt->execute($params);

        return array_map([$this, 'montar'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare('SELECT * FROM publicacao_encontrado WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => (int) $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? $this->montar($row) : null;
    }

    public function existe($id): bool
    {
        $stmt = $this->conn->prepare('SELECT 1 FROM publicacao_encontrado WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => (int) $id]);
        return (bool) $stmt->fetchColumn();
    }

    /** Atualiza o status (recebe valor no formato do app, com underscore). */
    public function atualizarStatus($id, $statusApp)
    {
        $stmt = $this->conn->prepare('UPDATE publicacao_encontrado SET status = :status WHERE id = :id');
        $stmt->execute([':status' => $this->statusParaDb($statusApp), ':id' => (int) $id]);
        return $this->buscarPorId($id);
    }

    /**
     * Ranking de rastreadores: soma de pontos e total de reports por usuário.
     * Resolve nome/foto pelo perfil correspondente ao login.
     */
    public function ranking(): array
    {
        $stmt = $this->conn->query(
            "SELECT fk_login_id, COUNT(*) AS total_reports, COALESCE(SUM(pontos),0) AS pontos
             FROM publicacao_encontrado
             GROUP BY fk_login_id
             ORDER BY pontos DESC, total_reports DESC, fk_login_id ASC"
        );
        $linhas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $out      = [];
        $posicao  = 1;
        foreach ($linhas as $l) {
            $usuario = $this->resolverUsuario((int) $l['fk_login_id']);
            $out[] = [
                'posicao'       => $posicao++,
                'usuario_id'    => (int) $l['fk_login_id'],
                'nome'          => $usuario['nome'],
                'foto'          => $usuario['foto'],
                'total_reports' => (int) $l['total_reports'],
                'pontos'        => (int) $l['pontos'],
            ];
        }
        return $out;
    }

    // ----------------------------------------------------------------- helpers

    /** Monta o shape de Avistamento (nomes do app via de-para). */
    private function montar(array $r): array
    {
        $fotos = [];
        if (!empty($r['fotos'])) {
            $dec = json_decode($r['fotos'], true);
            if (is_array($dec)) {
                $fotos = $dec;
            }
        }

        $usuario = $this->resolverUsuario((int) $r['fk_login_id']);

        return [
            'id'               => (int) $r['id'],
            'usuario_id'       => (int) $r['fk_login_id'],
            'usuario'          => [
                'id'   => (int) $r['fk_login_id'],
                'nome' => $usuario['nome'],
                'foto' => $usuario['foto'],
            ],
            'fk_animal_id'     => $r['fk_animal_id'] !== null ? (int) $r['fk_animal_id'] : null,
            'data'             => $r['data_encontro'],
            'especie'          => $r['especie'],
            'condicao'         => $r['condicao_fisica'],
            'acoes_tomadas'    => $r['acoes_realizadas'],
            'status'           => $this->statusParaApp($r['status']),
            'lat'              => $r['latitude'] !== null ? (float) $r['latitude'] : null,
            'lng'              => $r['longitude'] !== null ? (float) $r['longitude'] : null,
            'endereco_texto'   => $r['localizacao'] !== '' ? $r['localizacao'] : null,
            'fotos'            => $fotos,
            'foto'             => !empty($r['foto']) ? $r['foto'] : null,
            'pontos'           => $r['pontos'] !== null ? (int) $r['pontos'] : 0,
            'local_cep'        => $r['local_cep'],
            'local_logradouro' => $r['local_logradouro'],
            'local_numero'     => $r['local_numero'],
            'local_bairro'     => $r['local_bairro'],
            'local_cidade'     => $r['local_cidade'],
            'local_estado'     => $r['local_estado'],
        ];
    }

    /** Nome/foto do reporter conforme o tipo do login. */
    private function resolverUsuario($loginId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT l.tipo_usuario,
                    COALESCE(ad.nome, o.nome, v.nome) AS nome,
                    COALESCE(ad.avatar, o.avatar, v.avatar) AS foto
             FROM login l
             LEFT JOIN adotante ad ON ad.fk_login_id = l.id
             LEFT JOIN ong o ON o.fk_login_id = l.id
             LEFT JOIN veterinario v ON v.fk_login_id = l.id
             WHERE l.id = :id LIMIT 1"
        );
        $stmt->execute([':id' => (int) $loginId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return [
            'nome' => $row && $row['nome'] ? $row['nome'] : 'Usuário',
            'foto' => $row && !empty($row['foto']) ? $row['foto'] : null,
        ];
    }

    /** app (underscore) → banco (espaço). */
    private function statusParaDb($status)
    {
        return str_replace('_', ' ', trim((string) $status));
    }

    /** banco (espaço) → app (underscore). */
    private function statusParaApp($status)
    {
        return str_replace(' ', '_', trim((string) $status));
    }

    private function dataParaSql($valor)
    {
        if (empty($valor)) {
            return date('Y-m-d');
        }
        $ts = strtotime($valor);
        return $ts === false ? date('Y-m-d') : date('Y-m-d', $ts);
    }

    private function numOuNull($v)
    {
        return ($v === null || $v === '') ? null : $v;
    }
}
