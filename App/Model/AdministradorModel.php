<?php

namespace App\Model;

class AdministradorModel {

    private $adm_id;
    private $adm_nome;
    private $adm_cpf;
    private $adm_cep;
    private $adm_logradouro;
    private $adm_estado;
    private $adm_complemento;
    private $adm_dn;
    private $adm_cidade;
    private $adm_bairro;
    private $adm_numero; // adicionar no bd
    private $adm_tel1;
    private $adm_tel2;

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }

    public function __get($nome) {
        return $this->$nome;
    }

}