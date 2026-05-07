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