<?php

namespace App\Model;

class AnimalModel {

    private $ani_id;
    private $ani_nome;
    private $ani_sexo;
    private $ani_data_nascimento;

    public function __get($nome) {
        return $this->$nome;
    }

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }
}
