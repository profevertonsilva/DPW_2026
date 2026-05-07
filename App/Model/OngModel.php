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

    public function __construct(
        $ong_id = null,
        $ong_nome = null,
        $ong_cnpj = null,
        $ong_qnt_animais = null,
        $ong_cep = null,
        $ong_estado = null,
        $ong_cidade = null,
        $ong_bairro = null,
        $ong_logradouro = null,
        $ong_numero = null,
        $ong_complemento = null,
        $ong_tel1 = null,
        $ong_tel2 = null,
        $ong_status = null
    ) {

        $this->ong_id = $ong_id;
        $this->ong_nome = $ong_nome;
        $this->ong_cnpj = $ong_cnpj;
        $this->ong_qnt_animais = $ong_qnt_animais;
        $this->ong_cep = $ong_cep;
        $this->ong_estado = $ong_estado;
        $this->ong_cidade = $ong_cidade;
        $this->ong_bairro = $ong_bairro;
        $this->ong_logradouro = $ong_logradouro;
        $this->ong_numero = $ong_numero;
        $this->ong_complemento = $ong_complemento;
        $this->ong_tel1 = $ong_tel1;
        $this->ong_tel2 = $ong_tel2;
        $this->ong_status = $ong_status;
    }

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }

    public function __get($nome) {
        return $this->$nome;
    }
}