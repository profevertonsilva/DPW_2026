<?php 

namespace App\DAO;

use App\DAO;
use App\Model\VetClinicaModel;
use FW\Controller\FuncoesGlobais;

class VetClinicaDAO extends DAO {
    public function inserir($obj) {
        try{
            $fk_veterinario_id =  $obj->__get('fk_veterinario_id');
            $fk_clinica_id  =  $obj->__get('fk_clinica_id');

            $sql = "INSERT INTO vetclinica 
            (
                $fk_veterinario_id
                $fk_clinica_id
            ) VALUES (
                :fk_veterinario_id
                :fk_clinica_id
            )";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue('fk_veterinario_id', $fk_veterinario_id);
            $stmt->bindValue('fk_clinica_id', $fk_clinica_id);
            $stmt->excecute();
        }
        catch(\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function listar(){
        try{

        }
        catch(\PDOException $ex){
            header('Location:/error103');
            die();
        }    
    }

    public function excluir($obj) {}
    public function alterar($obj) {}
    public function buscarPorId($id){ }
    
}