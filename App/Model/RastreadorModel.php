<?php

namespace App\Model;

class RastreadorModel {

    private $rast_id;
    private $rast_nome;
    private $rast_cpf;
    private $rast_cep;
    private $rast_estado;
    private $rast_cidade;
    private $rast_bairro;
    private $rast_logradouro;
    private $rast_numero;
    private $rast_complemento;
    private $rast_tel1;
    private $rast_tel2;


    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }

    public function __get($nome) {
        return $this->$nome;
    }
}