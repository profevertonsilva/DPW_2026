<?php

namespace App\Model;

class ClinicaModel {

    private $id;
    private $cnpj;
    private $nome;
    private $cep;
    private $estado;
    private $bairro;
    private $logradouro;
    private $cidade;
    private $numero;
    private $complemento;
    private $telefone_1;
    private $telefone_2;

    public function __get($nome) {
        return $this->$nome;
    }

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'cnpj' => $this->cnpj,
            'nome' => $this->nome,
            'cep' => $this->cep,
            'estado' => $this->estado,
            'bairro' => $this->bairro,
            'logradouro' => $this->logradouro,
            'cidade' => $this->cidade,
            'numero' => $this->numero,
            'complemento' => $this->complemento,
            'telefone_1' => $this->telefone_1,
            'telefone_2' => $this->telefone_2
        ];
    }
}