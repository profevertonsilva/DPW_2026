<?php
/*
 * @Author marcus rito
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
            $anl_nome = $obj->__get('anl_nome');
            $anl_dn   = $obj->__get('anl_dn');
            $anl_sexo = $obj->__get('anl_sexo');

            $sql = "INSERT INTO animal (
                        nome,
                        data_nascimento,
                        sexo
                    ) VALUES (
                        :anl_nome,
                        :anl_dn,
                        :anl_sexo
                    )";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':anl_nome', $anl_nome);
            $stmt->bindValue(':anl_dn',   $anl_dn);
            $stmt->bindValue(':anl_sexo', $anl_sexo);
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
            $anl_id   = $obj->__get('anl_id');
            $anl_nome = $obj->__get('anl_nome');
            $anl_dn   = $obj->__get('anl_dn');
            $anl_sexo = $obj->__get('anl_sexo');

            $sql = "UPDATE animal SET
                        nome            = :anl_nome,
                        data_nascimento = :anl_dn,
                        sexo            = :anl_sexo
                    WHERE
                        id = :anl_id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':anl_id',   $anl_id,   \PDO::PARAM_INT);
            $stmt->bindValue(':anl_nome', $anl_nome);
            $stmt->bindValue(':anl_dn',   $anl_dn);
            $stmt->bindValue(':anl_sexo', $anl_sexo);
            $stmt->execute();
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function excluir($obj)
    {
        try {
            $anl_id = $obj->__get('anl_id');

            $sql = "DELETE FROM animal WHERE id = :anl_id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':anl_id', $anl_id, \PDO::PARAM_INT);
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
