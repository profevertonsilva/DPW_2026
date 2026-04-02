<?php

namespace App\Model;

class AnimalModel {

    private $anl_id;
    private $anl_nome;
    private $anl_sexo;
    private $anl_dn;
    private $fk_especie;

    public function __get($nome) {
        return $this->$nome;
    }

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }
}