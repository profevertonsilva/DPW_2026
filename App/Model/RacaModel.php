<?php 

namespace App\Model;

class RacaModel {

    private $id;
    private $raca_nome;
    private $fk_especie_id;

    public function __get($nome) {
        return $this->$nome;
    }

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }
}