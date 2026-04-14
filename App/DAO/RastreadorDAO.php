<?php 

namespace App\DAO;

use App\DAO;
use App\Model\RastreadorModel;
use FW\Controller\FuncoesGlobais;

class RastreadorDAO extends DAO {
    public function inserir($obj) {
        try{
            $rast_nome =  $obj->__get('rast_nome');
            $rast_cpf =  $obj->__get('rast_cpf');
            $rast_cep =  $obj->__get('rast_cep');
            $rast_estado =  $obj->__get('rast_estado');
            $rast_cidade =  $obj->__get('rast_cidade');
            $rast_bairro =  $obj->__get('rast_bairro');
            $rast_logradouro =  $obj->__get('rast_logradouro');
            $rast_complemento =  $obj->__get('rast_complemento');
            $rast_tel1 =  $obj->__get('rast_tel1');
            $rast_tel2 =  $obj->__get('rast_tel2');

            $sql = "INSERT INTO solicitacaoadocao 
            (
                $rast_nome
                $rast_cpf
                $rast_cep
                $rast_estado
                $rast_cidade
                $rast_bairro
                $rast_logradouro
                $rast_complemento
                $rast_tel1
                $rast_tel2
            ) VALUES (
                :rast_nome
                :rast_cpf
                :rast_cep
                :rast_estado
                :rast_cidade
                :rast_bairro
                :rast_logradouro
                :rast_complemento
                :rast_tel1
                :rast_tel2
            )";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue('rast_nome', $rast_nome);
            $stmt->bindValue('rast_cpf', $rast_cpf);
            $stmt->bindValue('rast_cep', $rast_cep);
            $stmt->bindValue('rast_estado', $rast_estado);
            $stmt->bindValue('rast_cidade', $rast_cidade);
            $stmt->bindValue('rast_bairro', $rast_bairro);
            $stmt->bindValue('rast_logradouro', $rast_logradouro);
            $stmt->bindValue('rast_complemento', $rast_complemento);
            $stmt->bindValue('rast_tel1', $rast_tel1);
            $stmt->bindValue('rast_tel2', $rast_tel2);
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