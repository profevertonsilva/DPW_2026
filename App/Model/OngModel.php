<?php

namespace App\Model;

class OngModel {

    private $id;
    private $nome;
    private $cnpj;
    private $qnt_animais;
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

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }

    public function __get($nome) {
        return $this->$nome;
    }
}