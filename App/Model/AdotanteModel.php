<?php

namespace App\Model;

class AdotanteModel {

    private $id;
    private $nome;
    private $cpf;
    private $data_nascimento;
    private $cep;
    private $estado;
    private $cidade;
    private $bairro;
    private $logradouro;
    private $numero;
    private $complemento;
    private $telefone_1;
    private $telefone_2;
    private $status;
    private $fk_login_id;

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }

    public function __get($nome) {
        return $this->$nome;
    }

}