<?php

namespace App\Model;

class AnimalRaca {
    private $anlRa_id;
    private $fk_animal_id;
    private $fk_raca_id;

    public function __get($nome) {
        return $this->$nome;
    }

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }
}