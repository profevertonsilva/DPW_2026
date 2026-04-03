<?php 

namespace App\Model;

class RacaModel {

    private $rc_id;
    private $rc_nome;

    public __get($nome) {
        return $this->$nome;
    }

    public __set($nome, $valor) {
        $this->$nome = $valor;
    }
}