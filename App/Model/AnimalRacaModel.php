<?php

namespace App\Model;

class AnimalRaca {
    private $anlRa_id;
    private $fk_animal_id;
    private $fk_raca_id;

    public __get($nome) {
        return $this->$nome;
    }

    public __set($nome, $valor) {
        $this->$nome = $valor;
    }
}