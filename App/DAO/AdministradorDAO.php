<?php 

namespace App\DAO;

use App\DAO;
use App\Model\AdministradorModel;
use FW\Controller\FuncoesGlobais;

class AdministradorDAO extends DAO {
    public function inserir($obj) {
        try {
             $adm_nome =  $obj->__get('adm_nome');
             $adm_cpf = $obj->__get('adm_cpf');
            //  $adm_cep;
            //  $adm_logradouro;
            //  $adm_estado;
            //  $adm_complemento;
            //  $adm_dn;
            //  $adm_cidade;
            //  $adm_bairro;
            //  $adm_numero; // adicionar no bd
            //  $adm_tel1;
            //  $adm_tel2;
        }catch(\PDOException $ex) {
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