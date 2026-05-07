<?php 

namespace App\DAO;

use App\DAO;
use App\Model\VeterinarioModel;
use FW\Controller\FuncoesGlobais;

class VeterinarioDAO extends DAO {
    public function inserir($obj) {
        try{
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
            $stmt->bindValue('vet_nome', $vet_nome);
            $stmt->bindValue('vet_cpf', $vet_cpf);
            $stmt->bindValue('vet_crmv', $vet_dn);
            $stmt->bindValue('vet_dn', $vet_cep);
            $stmt->bindValue('vet_cep', $vet_estado);
            $stmt->bindValue('vet_estado', $vet_cidade);
            $stmt->bindValue('vet_cidade', $vet_bairro);
            $stmt->bindValue('vet_bairro', $vet_logradouro);
            $stmt->bindValue('vet_logradouro', $vet_tel1);
            $stmt->bindValue('vet_numero', $vet_numero);
            $stmt->bindValue('vet_complemento', $vet_complemento);
            $stmt->bindValue('vet_tel1', $vet_tel1);
            $stmt->bindValue('vet_tel2', $vet_tel2);
            $stmt->excecute();
        }
        catch(\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function listar(){
        try{
            $veterinarios = array();

            $sql = "SELECT * FROM veterinario";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->excecute();
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach($resultado as $row){
                $vet = new VeterinarioModel();

                $global = new FuncoesGlobais();
                $global->popular_model($vet, $row);

                array_push($veterinarios, $vet);
            }

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