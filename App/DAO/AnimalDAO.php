<?php
/*
 * @Author marcus rito
 * Refatorado para suporte completo a novos campos e tratamento estável de erros - 2026
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
            $nome            = $obj->__get('nome');
            $data_nascimento = $obj->__get('data_nascimento');
            $sexo            = $obj->__get('sexo');
            $cor             = $obj->__get('cor');
            $porte           = $obj->__get('porte');
            $status          = $obj->__get('status');
            $castrado        = $obj->__get('castrado');
            $foto            = $obj->__get('foto');
            $descricao       = $obj->__get('descricao');

            $sql = "INSERT INTO animal (
                        nome,
                        data_nascimento,
                        sexo,
                        cor,
                        porte,
                        status,
                        castrado,
                        foto,
                        descricao
                    ) VALUES (
                        :nome,
                        :data_nascimento,
                        :sexo,
                        :cor,
                        :porte,
                        :status,
                        :castrado,
                        :foto,
                        :descricao
                    )";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':nome',            $nome);
            $stmt->bindValue(':data_nascimento', $data_nascimento);
            $stmt->bindValue(':sexo',            $sexo);
            $stmt->bindValue(':cor',             $cor);
            $stmt->bindValue(':porte',           $porte);
            $stmt->bindValue(':status',          $status);
            $stmt->bindValue(':castrado',        $castrado);
            $stmt->bindValue(':foto',            $foto);
            $stmt->bindValue(':descricao',       $descricao);
            $stmt->execute();
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao inserir animal no banco: " . $ex->getMessage());
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
            throw new \Exception("Erro ao listar animais: " . $ex->getMessage());
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
            throw new \Exception("Erro ao buscar animal por ID: " . $ex->getMessage());
        }
    }

    public function alterar($obj)
    {
        try {
            $id              = $obj->__get('id');
            $nome            = $obj->__get('nome');
            $data_nascimento = $obj->__get('data_nascimento');
            $sexo            = $obj->__get('sexo');
            $cor             = $obj->__get('cor');
            $porte           = $obj->__get('porte');
            $status          = $obj->__get('status');
            $castrado        = $obj->__get('castrado');
            $foto            = $obj->__get('foto');
            $descricao       = $obj->__get('descricao');

            $sql = "UPDATE animal SET
                        nome            = :nome,
                        data_nascimento = :data_nascimento,
                        sexo            = :sexo,
                        cor             = :cor,
                        porte           = :porte,
                        status          = :status,
                        castrado        = :castrado,
                        foto            = :foto,
                        descricao       = :descricao
                    WHERE
                        id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id',              $id, \PDO::PARAM_INT);
            $stmt->bindValue(':nome',            $nome);
            $stmt->bindValue(':data_nascimento', $data_nascimento);
            $stmt->bindValue(':sexo',            $sexo);
            $stmt->bindValue(':cor',             $cor);
            $stmt->bindValue(':porte',           $porte);
            $stmt->bindValue(':status',          $status);
            $stmt->bindValue(':castrado',        $castrado);
            $stmt->bindValue(':foto',            $foto);
            $stmt->bindValue(':descricao',       $descricao);
            $stmt->execute();
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao alterar animal no banco: " . $ex->getMessage());
        }
    }

    public function excluir($obj)
    {
        try {
            // Aceita tanto o ID puro quanto o objeto completo para evitar erros de chamada
            $id = is_object($obj) ? $obj->__get('id') : $obj;

            $sql = "DELETE FROM animal WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao excluir animal do banco: " . $ex->getMessage());
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
            throw new \Exception("Erro ao buscar por logado: " . $ex->getMessage());
        }
    }
}