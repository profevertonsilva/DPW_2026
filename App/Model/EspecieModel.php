<?php
/*
 * @Author marcus rito
 */

namespace App\Model;

class EspecieModel
{
    private $id;
    private $nome;

    public function __get($nome)
    {
        return $this->$nome;
    }

    public function __set($nome, $valor)
    {
        $this->$nome = $valor;
    }
}
