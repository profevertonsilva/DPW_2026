<?php

/**
 * @Author marcus rito
 * Sprint 2
 * 
 * Schema definitivo da tabela animal:
 *   id | nome | data_nascimento | sexo | fk_especie_id (FK) | cor
 *   castrado | descricao | porte | localizacao | foto | status
 *
 * Relacionamentos:
 *   especie    → fk_especie_id     (JOIN direto — integridade referencial)
 *   ONG        → pivot ong_animal  (LEFT JOIN)
 *   Raça       → pivot animal_raca (LEFT JOIN + GROUP_CONCAT)
 */

namespace App\DAO;

use App\DAO;
use App\Model\AnimalModel;
use FW\Controller\FuncoesGlobais;

class AnimalDAO extends DAO
{
    // CRUD BÁSICO

    /**
     * Insere um novo animal no banco.
     *
     * @param AnimalModel $obj
     * @return int ID gerado (0 em falha)
     */
    public function inserir($obj)
    {
        try {
            $nome            = $obj->__get('nome');
            $data_nascimento = $obj->__get('data_nascimento');
            $sexo            = $obj->__get('sexo');
            $fk_especie_id   = $obj->__get('fk_especie_id');
            $cor             = $obj->__get('cor');
            $castrado        = $obj->__get('castrado') ? 1 : 0;
            $descricao       = $obj->__get('descricao');
            $porte           = $obj->__get('porte');
            $localizacao     = $obj->__get('localizacao');
            $foto            = $obj->__get('foto');
            $status          = $obj->__get('status');

            $sql = "INSERT INTO animal (
                        nome,
                        data_nascimento,
                        sexo,
                        fk_especie_id,
                        cor,
                        castrado,
                        descricao,
                        porte,
                        localizacao,
                        foto,
                        status
                    ) VALUES (
                        :nome,
                        :data_nascimento,
                        :sexo,
                        :fk_especie_id,
                        :cor,
                        :castrado,
                        :descricao,
                        :porte,
                        :localizacao,
                        :foto,
                        :status
                    )";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':nome',            $nome);
            $stmt->bindValue(':data_nascimento', $data_nascimento);
            $stmt->bindValue(':sexo',            $sexo);
            $stmt->bindValue(':fk_especie_id',   $fk_especie_id,  \PDO::PARAM_INT);
            $stmt->bindValue(':cor',             $cor);
            $stmt->bindValue(':castrado',        $castrado,       \PDO::PARAM_INT);
            $stmt->bindValue(':descricao',       $descricao);
            $stmt->bindValue(':porte',           $porte);
            $stmt->bindValue(':localizacao',     $localizacao);
            $stmt->bindValue(':foto',            $foto);
            $stmt->bindValue(':status',          $status);
            $stmt->execute();

            return (int) $this->getConn()->lastInsertId();

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    /**
     * Exclui um animal pelo ID.
     * ON DELETE CASCADE cuida de: ong_animal, animal_raca,
     * historico_animal e solicitacao_adocao.
     *
     * @param int $id
     */
    public function excluir($id)
    {
        try {
            $sql = "DELETE FROM animal WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    /**
     * Atualiza os dados de um animal existente.
     *
     * @param AnimalModel $obj Objeto com id + dados novos
     */
    public function alterar($obj)
    {
        try {
            $id              = $obj->__get('id');
            $nome            = $obj->__get('nome');
            $data_nascimento = $obj->__get('data_nascimento');
            $sexo            = $obj->__get('sexo');
            $fk_especie_id   = $obj->__get('fk_especie_id');
            $cor             = $obj->__get('cor');
            $castrado        = $obj->__get('castrado') ? 1 : 0;
            $descricao       = $obj->__get('descricao');
            $porte           = $obj->__get('porte');
            $localizacao     = $obj->__get('localizacao');
            $foto            = $obj->__get('foto');
            $status          = $obj->__get('status');

            $sql = "UPDATE animal
                    SET
                        nome            = :nome,
                        data_nascimento = :data_nascimento,
                        sexo            = :sexo,
                        fk_especie_id   = :fk_especie_id,
                        cor             = :cor,
                        castrado        = :castrado,
                        descricao       = :descricao,
                        porte           = :porte,
                        localizacao     = :localizacao,
                        foto            = :foto,
                        `status`        = :status
                    WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id',              $id,             \PDO::PARAM_INT);
            $stmt->bindValue(':nome',            $nome);
            $stmt->bindValue(':data_nascimento', $data_nascimento);
            $stmt->bindValue(':sexo',            $sexo);
            $stmt->bindValue(':fk_especie_id',   $fk_especie_id,  \PDO::PARAM_INT);
            $stmt->bindValue(':cor',             $cor);
            $stmt->bindValue(':castrado',        $castrado,       \PDO::PARAM_INT);
            $stmt->bindValue(':descricao',       $descricao);
            $stmt->bindValue(':porte',           $porte);
            $stmt->bindValue(':localizacao',     $localizacao);
            $stmt->bindValue(':foto',            $foto);
            $stmt->bindValue(':status',          $status);
            $stmt->execute();

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    /**
     * Busca um animal pelo ID com todos os dados relacionados.
     *
     * JOINs:
     *   especie    → nome da espécie (JOIN direto via fk_especie_id)
     *   ong_animal → ong: nome da ONG responsável
     *   animal_raca → raca: raças concatenadas
     *
     * @param  int $id
     * @return AnimalModel|false
     */
    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT
                        a.*,
                        e.nome  AS especie_nome,
                        TIMESTAMPDIFF(MONTH, a.data_nascimento, CURDATE()) AS idade_meses,
                        o.id    AS ong_id,
                        o.nome  AS ong_nome,
                        GROUP_CONCAT(r.nome ORDER BY r.nome SEPARATOR ', ') AS racas
                    FROM animal a
                    LEFT JOIN especie      e  ON e.id = a.fk_especie_id
                    LEFT JOIN ong_animal  oa  ON oa.fk_animal_id = a.id
                    LEFT JOIN ong          o  ON o.id = oa.fk_ong_id
                    LEFT JOIN animal_raca  ar ON ar.fk_animal_id = a.id
                    LEFT JOIN raca         r  ON r.id = ar.fk_raca_id
                    WHERE a.id = :id
                    GROUP BY a.id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($resultado !== false) {
                $animalModel = new AnimalModel();

                $global = new FuncoesGlobais();
                $global->popularModel($animalModel, $resultado);

                return $animalModel;
            }

            return false;

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    /**
     * Lista todos os animais com dados relacionados.
     *
     * @return AnimalModel[]
     */
    public function listar()
    {
        try {
            $animais = array();

            $sql = "SELECT
                        a.*,
                        e.nome  AS especie_nome,
                        TIMESTAMPDIFF(MONTH, a.data_nascimento, CURDATE()) AS idade_meses,
                        o.id    AS ong_id,
                        o.nome  AS ong_nome,
                        GROUP_CONCAT(r.nome ORDER BY r.nome SEPARATOR ', ') AS racas
                    FROM animal a
                    LEFT JOIN especie      e  ON e.id = a.fk_especie_id
                    LEFT JOIN ong_animal  oa  ON oa.fk_animal_id = a.id
                    LEFT JOIN ong          o  ON o.id = oa.fk_ong_id
                    LEFT JOIN animal_raca  ar ON ar.fk_animal_id = a.id
                    LEFT JOIN raca         r  ON r.id = ar.fk_raca_id
                    GROUP BY a.id
                    ORDER BY a.nome ASC";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($resultado as $row) {
                $animalModel = new AnimalModel();

                $global = new FuncoesGlobais();
                $global->popularModel($animalModel, $row);

                array_push($animais, $animalModel);
            }

            return $animais;

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    // =========================================================================
    // LISTAGENS ESPECIALIZADAS
    // =========================================================================

    /**
     * Lista apenas animais com status = 'disponivel'.
     * Vitrine pública — não requer autenticação.
     *
     * @return AnimalModel[]
     */
    public function listarDisponiveis()
    {
        try {
            $animais = array();

            $sql = "SELECT
                        a.*,
                        e.nome  AS especie_nome,
                        TIMESTAMPDIFF(MONTH, a.data_nascimento, CURDATE()) AS idade_meses,
                        o.id    AS ong_id,
                        o.nome  AS ong_nome,
                        GROUP_CONCAT(r.nome ORDER BY r.nome SEPARATOR ', ') AS racas
                    FROM animal a
                    LEFT JOIN especie      e  ON e.id = a.fk_especie_id
                    LEFT JOIN ong_animal  oa  ON oa.fk_animal_id = a.id
                    LEFT JOIN ong          o  ON o.id = oa.fk_ong_id
                    LEFT JOIN animal_raca  ar ON ar.fk_animal_id = a.id
                    LEFT JOIN raca         r  ON r.id = ar.fk_raca_id
                    WHERE a.`status` = 'disponivel'
                    GROUP BY a.id
                    ORDER BY a.nome ASC";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($resultado as $row) {
                $animalModel = new AnimalModel();

                $global = new FuncoesGlobais();
                $global->popularModel($animalModel, $row);

                array_push($animais, $animalModel);
            }

            return $animais;

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    /**
     * Busca animais com filtros dinâmicos (RF#03).
     *
     * Como funciona:
     *   1. Query base sem WHERE/HAVING
     *   2. Cada filtro recebido adiciona fragmento em $condicoes + valor em $parametros
     *   3. idade_min / idade_max usam HAVING porque idade_meses é alias de expressão
     *      (MySQL não aceita alias do SELECT dentro do WHERE da mesma query)
     *   4. bindValue() em loop garante PDO::PARAM_INT para inteiros
     *
     * Filtros suportados:
     *   fk_especie_id → int   (ID da espécie — integridade garantida pela FK)
     *   porte         → enum: pequeno|medio|grande
     *   sexo          → enum: m|f
     *   castrado      → 0 ou 1
     *   status        → enum: disponivel|adotado|em_tratamento|reservado
     *   cor           → string (busca exata)
     *   localizacao   → string (busca LIKE %valor%)
     *   idade_min     → int meses (inclusivo)
     *   idade_max     → int meses (inclusivo)
     *   ong_id        → int
     *
     * @param  array $filtros
     * @return AnimalModel[]
     */
    public function buscarComFiltros($filtros)
    {
        try {
            $animais = array();

            $sql = "SELECT
                        a.*,
                        e.nome  AS especie_nome,
                        TIMESTAMPDIFF(MONTH, a.data_nascimento, CURDATE()) AS idade_meses,
                        o.id    AS ong_id,
                        o.nome  AS ong_nome,
                        GROUP_CONCAT(r.nome ORDER BY r.nome SEPARATOR ', ') AS racas
                    FROM animal a
                    LEFT JOIN especie      e  ON e.id = a.fk_especie_id
                    LEFT JOIN ong_animal  oa  ON oa.fk_animal_id = a.id
                    LEFT JOIN ong          o  ON o.id = oa.fk_ong_id
                    LEFT JOIN animal_raca  ar ON ar.fk_animal_id = a.id
                    LEFT JOIN raca         r  ON r.id = ar.fk_raca_id";

            $condicoes  = array();
            $tendo      = array();
            $parametros = array();

            // --- WHERE: colunas reais ---

            if (!empty($filtros['fk_especie_id'])) {
                $condicoes[]                    = "a.fk_especie_id = :fk_especie_id";
                $parametros[':fk_especie_id']   = (int) $filtros['fk_especie_id'];
            }

            if (!empty($filtros['porte'])) {
                $condicoes[]          = "a.porte = :porte";
                $parametros[':porte'] = $filtros['porte'];
            }

            if (!empty($filtros['sexo'])) {
                $condicoes[]         = "a.sexo = :sexo";
                $parametros[':sexo'] = $filtros['sexo'];
            }

            if (!empty($filtros['cor'])) {
                $condicoes[]        = "a.cor = :cor";
                $parametros[':cor'] = $filtros['cor'];
            }

            if (!empty($filtros['status'])) {
                $condicoes[]           = "a.`status` = :status";
                $parametros[':status'] = $filtros['status'];
            }

            // LIKE: busca parcial por localização — ex.: "SP" encontra "São Paulo - SP"
            if (!empty($filtros['localizacao'])) {
                $condicoes[]               = "a.localizacao LIKE :localizacao";
                $parametros[':localizacao'] = '%' . $filtros['localizacao'] . '%';
            }

            // isset (não !empty) porque castrado=0 é valor válido e seria ignorado por !empty
            if (isset($filtros['castrado']) && $filtros['castrado'] !== '') {
                $condicoes[]             = "a.castrado = :castrado";
                $parametros[':castrado'] = (int)(bool) $filtros['castrado'];
            }

            if (!empty($filtros['ong_id'])) {
                $condicoes[]             = "oa.fk_ong_id = :ong_id";
                $parametros[':ong_id']   = (int) $filtros['ong_id'];
            }

            // --- HAVING: alias de expressão (não pode ir no WHERE) ---

            if (isset($filtros['idade_min']) && $filtros['idade_min'] !== '') {
                $tendo[]                  = "idade_meses >= :idade_min";
                $parametros[':idade_min'] = (int) $filtros['idade_min'];
            }

            if (isset($filtros['idade_max']) && $filtros['idade_max'] !== '') {
                $tendo[]                  = "idade_meses <= :idade_max";
                $parametros[':idade_max'] = (int) $filtros['idade_max'];
            }

            // --- Montar SQL final ---

            if (!empty($condicoes)) {
                $sql .= " WHERE " . implode(" AND ", $condicoes);
            }

            $sql .= " GROUP BY a.id";

            if (!empty($tendo)) {
                $sql .= " HAVING " . implode(" AND ", $tendo);
            }

            $sql .= " ORDER BY a.nome ASC";

            // bindValue() em loop para garantir PDO::PARAM_INT nos inteiros
            $stmt = $this->getConn()->prepare($sql);

            foreach ($parametros as $param => $valor) {
                $tipo = is_int($valor) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
                $stmt->bindValue($param, $valor, $tipo);
            }

            $stmt->execute();

            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($resultado as $row) {
                $animalModel = new AnimalModel();

                $global = new FuncoesGlobais();
                $global->popularModel($animalModel, $row);

                array_push($animais, $animalModel);
            }

            return $animais;

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
}