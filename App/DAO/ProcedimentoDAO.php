<?php

/**
 * DAO para a tabela procedimento.
 *
 * Política append-only (RNF#08):
 *   - inserir()  → permitido
 *   - alterar()  → permitido para correções
 *   - excluir()  → BLOQUEADO (lança exceção)
 *
 * Schema:
 *   id | nome | tipo | data | veterinario_nome | observacoes
 *   anexo_url | fk_animal_id | fk_login_id | criado_em | criado_por
 */

namespace App\DAO;

use App\DAO;
use App\Model\ProcedimentoModel;
use FW\Controller\FuncoesGlobais;

class ProcedimentoDAO extends DAO
{
    /**
     * Insere um novo procedimento (append-only).
     *
     * @param  ProcedimentoModel $obj
     * @return int               ID gerado (0 em falha)
     */
    public function inserir($obj)
    {
        try {
            $nome = $obj->__get("nome");
            $tipo = $obj->__get("tipo");
            $data = $obj->__get("data");
            $veterinario_nome = $obj->__get("veterinario_nome");
            $observacoes = $obj->__get("observacoes");
            $anexo_url = $obj->__get("anexo_url");
            $fk_animal_id = $obj->__get("fk_animal_id");
            $fk_login_id = $obj->__get("fk_login_id");
            $criado_por = $obj->__get("criado_por");

            $sql = "INSERT INTO procedimento (
                        nome,
                        tipo,
                        data,
                        veterinario_nome,
                        observacoes,
                        anexo_url,
                        fk_animal_id,
                        fk_login_id,
                        criado_por
                    ) VALUES (
                        :nome,
                        :tipo,
                        :data,
                        :veterinario_nome,
                        :observacoes,
                        :anexo_url,
                        :fk_animal_id,
                        :fk_login_id,
                        :criado_por
                    )";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(":nome", $nome);
            $stmt->bindValue(":tipo", $tipo);
            $stmt->bindValue(":data", $data);
            $stmt->bindValue(":veterinario_nome", $veterinario_nome);
            $stmt->bindValue(":observacoes", $observacoes);
            $stmt->bindValue(":anexo_url", $anexo_url);
            $stmt->bindValue(":fk_animal_id", $fk_animal_id, \PDO::PARAM_INT);
            $stmt->bindValue(":fk_login_id", $fk_login_id, \PDO::PARAM_INT);
            $stmt->bindValue(":criado_por", $criado_por);
            $stmt->execute();

            return (int) $this->getConn()->lastInsertId();
        } catch (\PDOException $ex) {
            header("Location:/error103");
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
            "Exclusão de procedimentos não permitida. O histórico é imutável (RNF#08).",
        );
    }

    /**
     * Atualiza um procedimento existente.
     *
     * @param ProcedimentoModel $obj
     */
    public function alterar($obj)
    {
        try {
            $id = $obj->__get("id");
            $nome = $obj->__get("nome");
            $tipo = $obj->__get("tipo");
            $data = $obj->__get("data");
            $veterinario_nome = $obj->__get("veterinario_nome");
            $observacoes = $obj->__get("observacoes");
            $anexo_url = $obj->__get("anexo_url");
            $fk_animal_id = $obj->__get("fk_animal_id");

            $sql = "UPDATE procedimento
                    SET
                        nome             = :nome,
                        tipo             = :tipo,
                        data             = :data,
                        veterinario_nome = :veterinario_nome,
                        observacoes      = :observacoes,
                        anexo_url        = :anexo_url,
                        fk_animal_id     = :fk_animal_id
                    WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(":id", $id, \PDO::PARAM_INT);
            $stmt->bindValue(":nome", $nome);
            $stmt->bindValue(":tipo", $tipo);
            $stmt->bindValue(":data", $data);
            $stmt->bindValue(":veterinario_nome", $veterinario_nome);
            $stmt->bindValue(":observacoes", $observacoes);
            $stmt->bindValue(":anexo_url", $anexo_url);
            $stmt->bindValue(":fk_animal_id", $fk_animal_id, \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException $ex) {
            header("Location:/error103");
            die();
        }
    }

    /**
     * Busca um procedimento pelo ID, incluindo nome do animal via JOIN.
     *
     * @param  int                 $id
     * @return ProcedimentoModel|false
     */
    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT
                        p.*,
                        a.nome AS animal_nome
                    FROM procedimento p
                    LEFT JOIN animal a ON a.id = p.fk_animal_id
                    WHERE p.id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(":id", $id, \PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($resultado !== false) {
                $procedimentoModel = new ProcedimentoModel();

                $global = new FuncoesGlobais();
                $global->popularModel($procedimentoModel, $resultado);

                return $procedimentoModel;
            }

            return false;
        } catch (\PDOException $ex) {
            header("Location:/error103");
            die();
        }
    }

    /**
     * Lista todos os procedimentos com nome do animal.
     *
     * @return ProcedimentoModel[]
     */
    public function listar()
    {
        try {
            $procedimentos = [];

            $sql = "SELECT
                        p.*,
                        a.nome AS animal_nome
                    FROM procedimento p
                    LEFT JOIN animal a ON a.id = p.fk_animal_id
                    ORDER BY p.data DESC, p.nome ASC";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($resultado as $row) {
                $procedimentoModel = new ProcedimentoModel();

                $global = new FuncoesGlobais();
                $global->popularModel($procedimentoModel, $row);

                array_push($procedimentos, $procedimentoModel);
            }

            return $procedimentos;
        } catch (\PDOException $ex) {
            header("Location:/error103");
            die();
        }
    }

    // =========================================================================
    // LISTAGENS ESPECIALIZADAS
    // =========================================================================

    /**
     * Lista procedimentos de um animal específico.
     *
     * @param  int $animalId
     * @return ProcedimentoModel[]
     */
    public function listarPorAnimal($animalId)
    {
        try {
            $procedimentos = [];

            $sql = "SELECT
                        p.*,
                        a.nome AS animal_nome
                    FROM procedimento p
                    LEFT JOIN animal a ON a.id = p.fk_animal_id
                    WHERE p.fk_animal_id = :animal_id
                    ORDER BY p.data DESC, p.nome ASC";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(":animal_id", $animalId, \PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($resultado as $row) {
                $procedimentoModel = new ProcedimentoModel();

                $global = new FuncoesGlobais();
                $global->popularModel($procedimentoModel, $row);

                array_push($procedimentos, $procedimentoModel);
            }

            return $procedimentos;
        } catch (\PDOException $ex) {
            header("Location:/error103");
            die();
        }
    }
}
