<?php

namespace App\Model;

class ClinicaModel {
    private $cln_id;
    private $cln_cnpj;
    private $cln_nome;
    private $cln_cep;
    private $cln_estado;
    private $cln_bairro;
    private $cln_logradouro;
    private $cln_cidade;
    private $cln_numero;
    private $cln_complemento;
    private $cln_tel1;
    private $cln_tel2;


    public function __get($nome) {
        return $this->$nome;
    }

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }
}