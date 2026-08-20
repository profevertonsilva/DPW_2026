<?php

namespace App\Model;

class RacaModel
{
    private $id;
    private $nome;
    private $fk_especie_id;
    private $especie_nome;  // populado via JOIN no DAO

    public function __get($nome)
    {
        return $this->$nome;
    }

    public function __set($nome, $valor)
    {
        $this->$nome = $valor;
    }
}
