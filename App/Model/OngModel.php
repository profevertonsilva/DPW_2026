<?php

namespace App\Model;

class OngModel {
    private $ong_id;
    private $ong_nome;
    private $ong_cnpj;
    private $ong_qnt_animais;
    private $ong_cep;
    private $ong_estado;
    private $ong_cidade;
    private $ong_bairro;
    private $ong_logradouro;
    private $ong_numero;
    private $ong_complemento;
    private $ong_tel1;
    private $ong_tel2;
    private $ong_status;

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }

    public function __get($nome) {
        return $this->$nome;
    }

}