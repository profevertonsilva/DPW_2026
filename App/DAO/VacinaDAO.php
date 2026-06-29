<?php

/**
 * DAO para a tabela vacina.
 *
 * Política append-only (RNF#08):
 *   - inserir()  → permitido (sempre adiciona novo registro)
 *   - alterar()  → permitido para correções
 *   - excluir()  → BLOQUEADO (lança exceção)
 *
 * Schema:
 *   id | nome | data_aplicacao | data_reforco | veterinario_nome
 *   clinica_nome | fk_animal_id | fk_login_id | criado_em | criado_por
 */

namespace App\DAO;

use App\DAO;
use App\Model\VacinaModel;
use FW\Controller\FuncoesGlobais;

class VacinaDAO extends DAO
{
    /**
     * Insere um novo registro de vacina (append-only).
     *
     * @param  VacinaModel $obj
     * @return int         ID gerado (0 em falha)
     */
    public function inserir($obj)
    {
        try {
            $nome             = $obj->__get('nome');
            $data_aplicacao   = $obj->__get('data_aplicacao');
            $data_reforco     = $obj->__get('data_reforco');
            $veterinario_nome = $obj->__get('veterinario_nome');
            $clinica_nome     = $obj->__get('clinica_nome');
            $fk_animal_id     = $obj->__get('fk_animal_id');
            $fk_login_id      = $obj->__get('fk_login_id');
            $criado_por       = $obj->__get('criado_por');

            $sql = "INSERT INTO vacina (
                        nome,
                        data_aplicacao,
                        data_reforco,
                        veterinario_nome,
                        clinica_nome,
                        fk_animal_id,
                        fk_login_id,
                        criado_por
                    ) VALUES (
                        :nome,
                        :data_aplicacao,
                        :data_reforco,
                        :veterinario_nome,
                        :clinica_nome,
                        :fk_animal_id,
                        :fk_login_id,
                        :criado_por
                    )";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':nome',             $nome);
            $stmt->bindValue(':data_aplicacao',   $data_aplicacao);
            $stmt->bindValue(':data_reforco',     $data_reforco);
            $stmt->bindValue(':veterinario_nome', $veterinario_nome);
            $stmt->bindValue(':clinica_nome',     $clinica_nome);
            $stmt->bindValue(':fk_animal_id',     $fk_animal_id,  \PDO::PARAM_INT);
            $stmt->bindValue(':fk_login_id',      $fk_login_id,   \PDO::PARAM_INT);
            $stmt->bindValue(':criado_por',       $criado_por);
            $stmt->execute();

            return (int) $this->getConn()->lastInsertId();

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    /**
     * Bloqueado por política append-only (RNF#08).
     *
     * @param  int $id
     * @throws \RuntimeException Sempre
     */
    public function excluir($id)
    {
        throw new \RuntimeException(
            'Exclusão de vacinas não permitida. O histórico de vacinação é imutável (RNF#08).'
        );
    }

    /**
     * Atualiza um registro de vacina existente.
     *
     * @param VacinaModel $obj
     */
    public function alterar($obj)
    {
        try {
            $id               = $obj->__get('id');
            $nome             = $obj->__get('nome');
            $data_aplicacao   = $obj->__get('data_aplicacao');
            $data_reforco     = $obj->__get('data_reforco');
            $veterinario_nome = $obj->__get('veterinario_nome');
            $clinica_nome     = $obj->__get('clinica_nome');
            $fk_animal_id     = $obj->__get('fk_animal_id');

            $sql = "UPDATE vacina
                    SET
                        nome             = :nome,
                        data_aplicacao   = :data_aplicacao,
                        data_reforco     = :data_reforco,
                        veterinario_nome = :veterinario_nome,
                        clinica_nome     = :clinica_nome,
                        fk_animal_id     = :fk_animal_id
                    WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id',               $id,              \PDO::PARAM_INT);
            $stmt->bindValue(':nome',             $nome);
            $stmt->bindValue(':data_aplicacao',   $data_aplicacao);
            $stmt->bindValue(':data_reforco',     $data_reforco);
            $stmt->bindValue(':veterinario_nome', $veterinario_nome);
            $stmt->bindValue(':clinica_nome',     $clinica_nome);
            $stmt->bindValue(':fk_animal_id',     $fk_animal_id,    \PDO::PARAM_INT);
            $stmt->execute();

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    /**
     * Busca uma vacina pelo ID, incluindo nome do animal via JOIN.
     *
     * @param  int        $id
     * @return VacinaModel|false
     */
    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT
                        v.*,
                        a.nome AS animal_nome
                    FROM vacina v
                    LEFT JOIN animal a ON a.id = v.fk_animal_id
                    WHERE v.id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($resultado !== false) {
                $vacinaModel = new VacinaModel();

                $global = new FuncoesGlobais();
                $global->popularModel($vacinaModel, $resultado);

                return $vacinaModel;
            }

            return false;

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    /**
     * Lista todas as vacinas com nome do animal.
     *
     * @return VacinaModel[]
     */
    public function listar()
    {
        try {
            $vacinas = array();

            $sql = "SELECT
                        v.*,
                        a.nome AS animal_nome
                    FROM vacina v
                    LEFT JOIN animal a ON a.id = v.fk_animal_id
                    ORDER BY v.data_aplicacao DESC, v.nome ASC";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($resultado as $row) {
                $vacinaModel = new VacinaModel();

                $global = new FuncoesGlobais();
                $global->popularModel($vacinaModel, $row);

                array_push($vacinas, $vacinaModel);
            }

            return $vacinas;

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    // =========================================================================
    // LISTAGENS ESPECIALIZADAS
    // =========================================================================

    /**
     * Lista todas as vacinas de um animal específico.
     *
     * @param  int $animalId
     * @return VacinaModel[]
     */
    public function listarPorAnimal($animalId)
    {
        try {
            $vacinas = array();

            $sql = "SELECT
                        v.*,
                        a.nome AS animal_nome
                    FROM vacina v
                    LEFT JOIN animal a ON a.id = v.fk_animal_id
                    WHERE v.fk_animal_id = :animal_id
                    ORDER BY v.data_aplicacao DESC, v.nome ASC";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':animal_id', $animalId, \PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($resultado as $row) {
                $vacinaModel = new VacinaModel();

                $global = new FuncoesGlobais();
                $global->popularModel($vacinaModel, $row);

                array_push($vacinas, $vacinaModel);
            }

            return $vacinas;

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    /**
     * Busca vacinas cuja data de reforço está dentro de um intervalo de N dias
     * a partir de hoje. Utilizado pelo sistema de alertas automáticos.
     *
     * @param  int $dias  Janela em dias (ex: 7, 15 ou 30)
     * @return VacinaModel[]
     */
    public function buscarReforcosProximos($dias = 7)
    {
        try {
            $vacinas = array();
            $dias    = (int) $dias;

            $sql = "SELECT
                        v.*,
                        a.nome AS animal_nome
                    FROM vacina v
                    LEFT JOIN animal a ON a.id = v.fk_animal_id
                    WHERE v.data_reforco IS NOT NULL
                      AND v.data_reforco BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :dias DAY)
                    ORDER BY v.data_reforco ASC";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':dias', $dias, \PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($resultado as $row) {
                $vacinaModel = new VacinaModel();

                $global = new FuncoesGlobais();
                $global->popularModel($vacinaModel, $row);

                array_push($vacinas, $vacinaModel);
            }

            return $vacinas;

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
}
