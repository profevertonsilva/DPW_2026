<?php

namespace App\Model;

class VetClinica {
     private $VetCli_id;
    private $fk_vet_id;
    private $fk_cli_id;

    public __get($nome) {
        return $this->$nome;
    }

    public __set($nome, $valor) {
        $this->$nome = $valor;
    }
}