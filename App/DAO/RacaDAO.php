<?php

namespace App\DAO;

use App\DAO;
use App\Model\RacaModel;
use FW\Controller\FuncoesGlobais;

class RacaDAO extends DAO
{
    public function inserir($obj)
    {
        try {
            $nome          = $obj->__get('nome');
            $fk_especie_id = $obj->__get('fk_especie_id');

            $sql = "INSERT INTO raca (nome, fk_especie_id)
                    VALUES (:nome, :fk_especie_id)";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':nome',          $nome);
            $stmt->bindValue(':fk_especie_id', $fk_especie_id, \PDO::PARAM_INT);
            $stmt->execute();

            return (int) $this->getConn()->lastInsertId();

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function excluir($id)
    {
        try {
            $sql = "DELETE FROM raca WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function alterar($obj)
    {
        try {
            $id            = $obj->__get('id');
            $nome          = $obj->__get('nome');
            $fk_especie_id = $obj->__get('fk_especie_id');

            $sql = "UPDATE raca
                    SET nome = :nome, fk_especie_id = :fk_especie_id
                    WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id',            $id,            \PDO::PARAM_INT);
            $stmt->bindValue(':nome',          $nome);
            $stmt->bindValue(':fk_especie_id', $fk_especie_id, \PDO::PARAM_INT);
            $stmt->execute();

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT r.id, r.nome, r.fk_especie_id, e.nome AS especie_nome
                    FROM   raca r
                    INNER JOIN especie e ON e.id = r.fk_especie_id
                    WHERE  r.id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($resultado !== false) {
                $model  = new RacaModel();
                $global = new FuncoesGlobais();
                $global->popularModel($model, $resultado);
                return $model;
            }

            return false;

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function listar()
    {
        try {
            $racas = array();

            $sql = "SELECT r.id, r.nome, r.fk_especie_id, e.nome AS especie_nome
                    FROM   raca r
                    INNER JOIN especie e ON e.id = r.fk_especie_id
                    ORDER  BY r.nome ASC";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($resultado as $row) {
                $model  = new RacaModel();
                $global = new FuncoesGlobais();
                $global->popularModel($model, $row);
                array_push($racas, $model);
            }

            return $racas;

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function listarPorEspecie($fk_especie_id)
    {
        try {
            $racas = array();

            $sql = "SELECT r.id, r.nome, r.fk_especie_id, e.nome AS especie_nome
                    FROM   raca r
                    INNER JOIN especie e ON e.id = r.fk_especie_id
                    WHERE  r.fk_especie_id = :fk_especie_id
                    ORDER  BY r.nome ASC";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':fk_especie_id', $fk_especie_id, \PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($resultado as $row) {
                $model  = new RacaModel();
                $global = new FuncoesGlobais();
                $global->popularModel($model, $row);
                array_push($racas, $model);
            }

            return $racas;

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
}
