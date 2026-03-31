<?php

namespace App\Model;

class AdotanteModel {

    private $adt_id;
    private $adt_nome;
    private $adt_cpf;
    private $adt_dn;
    private $adt_cep;
    private $adt_estado;
    private $adt_cidade;
    private $adt_bairro;
    private $adt_logradouro;
    private $adt_numero;
    private $adt_complemento;
    private $adt_tel1;
    private $adt_tel2;
    private $adt_status;

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }

    public function __get($nome) {
        return $this->$nome;
    }

}