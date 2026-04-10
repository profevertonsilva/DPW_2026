<?php

namespace App\DAO;


use App\DAO;
use App\Model\AdotanteModel;
use FW\Controller\FuncoesGlobais;

class AdotanteDAO extends DAO
{
    public  function inserir($obj)
    {
        $adt_nome = $obj->__get("adt_nome");
        $adt_cpf = $obj->__get("adt_cpf");
        $adt_dn = $obj->__get("adt_dn");
        $adt_cep = $obj->__get("adt_cep");
        $adt_estado = $obj->__get("adt_estado");
        $adt_cidade = $obj->__get("adt_cidade");
        $adt_bairro = $obj->__get("adt_bairro");
        $adt_logradouro = $obj->__get("adt_logradouro");
        $adt_numero = $obj->__get("adt_numero");
        $adt_complemento = $obj->__get("adt_complemento");
        $adt_tel1 = $obj->__get("adt_tel1");
        $adt_tel2 = $obj->__get("adt_tel2");
        $adt_status = $obj->__get("adt_status");


        $sql = "INSERT INTO adotante 
        (
            $adt_nome
            $adt_cpf
            $adt_dn
            $adt_cep
            $adt_estado
            $adt_cidade
            $adt_bairro
            $adt_logradouro
            $adt_numero
            $adt_complemento
            $adt_tel1
            $adt_tel2
            $adt_status
         ) VALUES (
            :adt_nome
            :adt_cpf
            :adt_dn
            :adt_cep
            :adt_estado
            :adt_cidade
            :adt_bairro
            :adt_logradouro
            :adt_numero
            :adt_complemento
            :adt_tel1
            :adt_tel2
            :adt_status
         )";

        $stmt = $this->getConn()->prepare($sql);
        $stmt->bindValue('adt_nome', $adt_nome);
        $stmt->bindValue('adt_cpf', $adt_cpf);
        $stmt->bindValue('adt_dn', $adt_dn);
        $stmt->bindValue('adt_cep', $adt_cep);
        $stmt->bindValue('adt_estado', $adt_estado);
        $stmt->bindValue('adt_cidade', $adt_cidade);
        $stmt->bindValue('adt_bairro', $adt_bairro);
        $stmt->bindValue('adt_logradouro', $adt_logradouro);
        $stmt->bindValue('adt_tel1', $adt_tel1);
        $stmt->bindValue('adt_tel2', $adt_tel2);
        $stmt->bindValue('adt_status', $adt_status);
        $stmt->excecute();
    }
    
    public  function excluir($obj) {}
    public  function alterar($obj) {}
    public  function buscarPorId($obj) {}
    public  function listar() {}
}
