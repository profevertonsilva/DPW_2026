<?php 

namespace App\DAO;

use App\DAO;
use App\Model\SolicitacaoAdocaoModel;
use FW\Controller\FuncoesGlobais;

class SolicitacaoAdocaoDAO extends DAO {
    public function inserir($obj) {
        try{
            $solAdc_data =  $obj->__get('solAdc_data');
            $solAdc_status =  $obj->__get('solAdc_status');
            $solAdc_motivo =  $obj->__get('solAdc_motivo');
            $fk_adotante_id =  $obj->__get('fk_adotante_id');
            $fk_animal_id =  $obj->__get('fk_animal_id');

            $sql = "INSERT INTO solicitacaoadocao 
            (
                $solAdc_data
                $solAdc_status
                $solAdc_motivo
                $fk_adotante_id
                $fk_animal_id
            ) VALUES (
                :solAdc_data
                :solAdc_status
                :solAdc_motivo
                :fk_adotante_id
                :fk_animal_id
            )";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue('solAdc_data', $solAdc_data);
            $stmt->bindValue('solAdc_status', $solAdc_status);
            $stmt->bindValue('solAdc_motivo', $solAdc_motivo);
            $stmt->bindValue('fk_adotante_id', $fk_adotante_id);
            $stmt->bindValue('fk_animal_id', $fk_animal_id);
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