<?php

namespace App\Model;

class OngAnimal {
     private $OngAnl_id;
    private $fk_ong_id;
    private $fk_animal_id;

    public __get($nome) {
        return $this->$nome;
    }

    public __set($nome, $valor) {
        $this->$nome = $valor;
    }
}