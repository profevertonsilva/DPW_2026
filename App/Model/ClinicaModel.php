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

    public function __construct(
        $cln_id = null,
        $cln_cnpj = null,
        $cln_nome = null,
        $cln_cep = null,
        $cln_estado = null,
        $cln_bairro = null,
        $cln_logradouro = null,
        $cln_cidade = null,
        $cln_numero = null,
        $cln_complemento = null,
        $cln_tel1 = null,
        $cln_tel2 = null
    ) {
        $this->cln_id = $cln_id;
        $this->cln_cnpj = $cln_cnpj;
        $this->cln_nome = $cln_nome;
        $this->cln_cep = $cln_cep;
        $this->cln_estado = $cln_estado;
        $this->cln_bairro = $cln_bairro;
        $this->cln_logradouro = $cln_logradouro;
        $this->cln_cidade = $cln_cidade;
        $this->cln_numero = $cln_numero;
        $this->cln_complemento = $cln_complemento;
        $this->cln_tel1 = $cln_tel1;
        $this->cln_tel2 = $cln_tel2;
    }

    public function __get($nome) {
        return $this->$nome;
    }

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }

    public function toArray() {
        return [
            'cln_id' => $this->cln_id,
            'cln_cnpj' => $this->cln_cnpj,
            'cln_nome' => $this->cln_nome,
            'cln_cep' => $this->cln_cep,
            'cln_estado' => $this->cln_estado,
            'cln_bairro' => $this->cln_bairro,
            'cln_logradouro' => $this->cln_logradouro,
            'cln_cidade' => $this->cln_cidade,
            'cln_numero' => $this->cln_numero,
            'cln_complemento' => $this->cln_complemento,
            'cln_tel1' => $this->cln_tel1,
            'cln_tel2' => $this->cln_tel2
        ];
    }
}