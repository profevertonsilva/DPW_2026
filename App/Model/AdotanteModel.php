<?php

namespace App\Model;

class AdotanteModel {

    // Atributos mapeados exatamente com as colunas do banco de dados (prefixo adt_)
    private $adt_id;
    private $adt_nome;
    private $adt_cpf;
    private $adt_dn; // Data de nascimento no banco costuma ser adt_dn
    private $adt_cep;
    private $adt_estado;
    private $adt_cidade;
    private $adt_bairro;
    private $adt_logradouro;
    private $adt_numero;
    private $adt_complemento;
    private $adt_tel1; // Telefone 1 no banco costuma ser adt_tel1
    private $adt_tel2; // Telefone 2 no banco costuma ser adt_tel2
    private $adt_status;
    private $fk_login_id;
    
    // Atributo para receber o e-mail vindo do LEFT JOIN na listagem
    private $email;

    // Métodos mágicos para permitir tanto $model->__set('nome', ...) quanto o mapeamento do banco
    public function __set($nome, $valor) {
        // Se o Controller tentar gravar 'nome' puro, nós convertemos para 'adt_nome' automaticamente
        if (property_exists($this, "adt_" . $nome)) {
            $propriedade = "adt_" . $nome;
            $this->$propriedade = $valor;
        } elseif ($nome === 'data_nascimento') {
            $this->adt_dn = $valor;
        } elseif ($nome === 'telefone_1') {
            $this->adt_tel1 = $valor;
        } elseif ($nome === 'telefone_2') {
            $this->adt_tel2 = $valor;
        } elseif ($nome === 'status') {
            $this->adt_status = $valor;
        } else {
            // Caso já venha com o prefixo adt_ do banco de dados
            $this->$nome = $valor;
        }
    }

    public function __get($nome) {
        // Da mesma forma, se o código pedir $model->__get('nome'), entregamos o 'adt_nome'
        if (property_exists($this, "adt_" . $nome)) {
            $propriedade = "adt_" . $nome;
            return $this->$propriedade;
        } elseif ($nome === 'data_nascimento') {
            return $this->adt_dn;
        } elseif ($nome === 'telefone_1') {
            return $this->adt_tel1;
        } elseif ($nome === 'telefone_2') {
            return $this->adt_tel2;
        } elseif ($nome === 'status') {
            return $this->adt_status;
        }
        
        return $this->$nome;
    }
}