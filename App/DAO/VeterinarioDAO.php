<?php 

namespace App\DAO;

use App\DAO;
use App\Model\VeterinarioModel;
use FW\Controller\FuncoesGlobais;

class VeterinarioDAO extends DAO {
    public function inserir($obj) {
        $vet_nome =  $obj->__get('vet_nome');
        $vet_cpf  =  $obj->__get('vet_cpf');
        $vet_crmv =  $obj->__get('vet_crmv');
        $vet_dn =  $obj->__get('vet_dn');
        $vet_cep =  $obj->__get('vet_cep');
        $vet_estado =  $obj->__get('vet_estado');
        $vet_cidade =  $obj->__get('vet_cidade');
        $vet_bairro =  $obj->__get('vet_bairro');
        $vet_logradouro =  $obj->__get('vet_logradouro');
        $vet_numero =  $obj->__get('vet_numero');
        $vet_complemento =  $obj->__get('vet_complemento');
        $vet_tel1 =  $obj->__get('vet_tel1');
        $vet_tel2 =  $obj->__get('vet_tel2');

        $sql = "INSERT INTO veterinario 
        (
            $vet_nome
            $vet_cpf
            $vet_crmv
            $vet_dn
            $vet_cep
            $vet_estado
            $vet_cidade
            $vet_bairro
            $vet_logradouro
            $vet_numero
            $vet_complemento
            $vet_tel1
            $vet_tel2
        ) VALUES (
            :vet_nome
            :vet_cpf
            :vet_crmv
            :vet_dn
            :vet_cep
            :vet_estado
            :vet_cidade
            :vet_bairro
            :vet_logradouro
            :vet_numero
            :vet_complemento
            :vet_tel1
            :vet_tel2
        )";

        $stmt = $this->getConn()->prepare($sql);
        $stmt->bindValue('vet_nome', $adt_nome);
        $stmt->bindValue('vet_cpf', $adt_cpf);
        $stmt->bindValue('vet_crmv', $adt_dn);
        $stmt->bindValue('vet_dn', $adt_cep);
        $stmt->bindValue('vet_cep', $adt_estado);
        $stmt->bindValue('vet_estado', $adt_cidade);
        $stmt->bindValue('vet_cidade', $adt_bairro);
        $stmt->bindValue('vet_bairro', $adt_logradouro);
        $stmt->bindValue('vet_logradouro', $adt_tel1);
        $stmt->bindValue('vet_numero', $adt_tel2);
        $stmt->bindValue('vet_complemento', $adt_status);
        $stmt->bindValue('vet_tel1', $adt_tel2);
        $stmt->bindValue('vet_tel2', $adt_status);
        $stmt->excecute();

        header('Location:/error103');
        die();
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