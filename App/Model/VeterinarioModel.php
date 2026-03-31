<?php

namespace App\Model;

class VeterinarioModel {
    private $vet_id;
    private $vet_nome;
    private $vet_cpf;
    private $vet_crmv;
    private $vet_dn;
    private $vet_cep;
    private $vet_estado;
    private $vet_cidade;
    private $vet_bairro;
    private $vet_logradouro;
    private $vet_numero;
    private $vet_complemento;
    private $vet_tel1;
    private $vet_tel2;

     public function __set($nome, $valor) {
        $this->$nome = $valor;
    }

    public function __get($nome) {
        return $this->$nome;
    }
}