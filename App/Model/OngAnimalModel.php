<?php

namespace App\Model;

class OngAnimalModel {

    private $onganl_id;
    private $fk_ong_id;
    private $fk_animal_id;

    public function __get($nome) {
        return $this->$nome;
    }

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }
}