<?php

namespace App\Model;

class VetClinica {
     private $VetCli_id;
    private $fk_veterinario_id;
    private $fk_clinica_id;

    public function __get($nome) {
        return $this->$nome;
    }

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }
}