<?php

namespace App\DAO;

use App\DAO;
use App\Model\OngAnimalModel;
use FW\Controller\FuncoesGlobais;

class OngAnimalDAO extends DAO {

    public function inserir($obj) {

        $ongAnimal = $obj;

        try {

            $sql = "INSERT INTO ong_animal (
                        fk_ong_id,
                        fk_animal_id
                    ) VALUES (
                        :fk_ong_id,
                        :fk_animal_id
                    )";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':fk_ong_id', $ongAnimal->__get('fk_ong_id'));
            $stmt->bindValue(':fk_animal_id', $ongAnimal->__get('fk_animal_id'));

            return $stmt->execute();

        } catch (\Exception $e) {

            return false;
        }
    }

    public function listar() {

        try {

            $ongAnimais = array();

            $sql = "SELECT * 
                    FROM ong_animal
                    ORDER BY onganl_id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->execute();

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach($result as $row) {

                $ongAnimal = new OngAnimalModel();

                $ongAnimal->__set('onganl_id', $row['onganl_id']);
                $ongAnimal->__set('fk_ong_id', $row['fk_ong_id']);
                $ongAnimal->__set('fk_animal_id', $row['fk_animal_id']);

                $ongAnimais[] = $ongAnimal;
            }

            return $ongAnimais;

        } catch (\Exception $e) {

            return false;
        }
    }

    public function buscarPorId($id) {

        try {

            $sql = "SELECT * 
                    FROM ong_animal
                    WHERE onganl_id = :onganl_id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':onganl_id', $id);

            $stmt->execute();

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if(!$row) {
                return false;
            }

            $ongAnimal = new OngAnimalModel();

            $ongAnimal->__set('onganl_id', $row['onganl_id']);
            $ongAnimal->__set('fk_ong_id', $row['fk_ong_id']);
            $ongAnimal->__set('fk_animal_id', $row['fk_animal_id']);

            return $ongAnimal;

        } catch (\Exception $e) {

            return false;
        }
    }

    public function alterar($obj) {

        $ongAnimal = $obj;

        try {

            $sql = "UPDATE ong_animal SET
                        fk_ong_id = :fk_ong_id,
                        fk_animal_id = :fk_animal_id
                    WHERE onganl_id = :onganl_id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':onganl_id', $ongAnimal->__get('onganl_id'));
            $stmt->bindValue(':fk_ong_id', $ongAnimal->__get('fk_ong_id'));
            $stmt->bindValue(':fk_animal_id', $ongAnimal->__get('fk_animal_id'));

            return $stmt->execute();

        } catch (\Exception $e) {

            return false;
        }
    }

    public function excluir($id) {

        try {

            $sql = "DELETE 
                    FROM ong_animal
                    WHERE onganl_id = :onganl_id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':onganl_id', $id);

            return $stmt->execute();

        } catch (\Exception $e) {

            return false;
        }
    }
}