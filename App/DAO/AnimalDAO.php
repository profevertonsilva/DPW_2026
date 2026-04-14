<?php

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
            $anl_dn = $obj->__get('anl_dn');
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
            $stmt->bindValue(':anl_dn', $anl_dn);
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
            $sql = "SELECT a.*
                    FROM animal a";

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

    public function excluir($obj) {}
    public function alterar($obj) {}
    public function buscarPorId($id) {}
    public function buscarPorLogado($id) {}
}
