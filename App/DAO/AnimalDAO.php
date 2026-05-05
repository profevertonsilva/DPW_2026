<?php
/*
 * @Author marcus rito
 * 
 * Data: 04/05/2026
 */

namespace App\DAO;

use App\DAO;
use App\Model\AnimalModel;
use FW\Controller\FuncoesGlobais;


class AnimalDAO extends DAO
{

    public function inserir($obj)
    {
        try {
            $nome = $obj->__get('nome');
            $data_nascimento   = $obj->__get('data_nascimento');
            $sexo = $obj->__get('sexo');

            $sql = "INSERT INTO animal (
                        nome,
                        data_nascimento,
                        sexo
                    ) VALUES (
                        :nome,
                        :data_nascimento,
                        :sexo
                    )";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':data_nascimento',   $data_nascimento);
            $stmt->bindValue(':sexo', $sexo);
            $stmt->execute();
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function listar()
    {
        try {
            $animais = array();

            $sql = "SELECT * FROM animal";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($result as $row) {
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

    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT * FROM animal WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($result) {
                $animalModel = new AnimalModel();
                $global = new FuncoesGlobais();
                $global->popularModel($animalModel, $result);
                return $animalModel;
            }

            return null;
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function alterar($obj)
    {
        try {
            $id   = $obj->__get('id');
            $nome = $obj->__get('nome');
            $data_nascimento   = $obj->__get('data_nascimento');
            $sexo = $obj->__get('sexo');

            $sql = "UPDATE animal SET
                        nome            = :nome,
                        data_nascimento = :data_nascimento,
                        sexo            = :sexo
                    WHERE
                        id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id',   $id,   \PDO::PARAM_INT);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':data_nascimento',   $data_nascimento);
            $stmt->bindValue(':sexo', $sexo);
            $stmt->execute();
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function excluir($obj)
    {
        try {
            $id = $obj->__get('id');

            $sql = "DELETE FROM animal WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function buscarPorLogado($id)
    {
        try {
            $sql = "SELECT * FROM animal WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $animais = array();
            foreach ($result as $row) {
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
