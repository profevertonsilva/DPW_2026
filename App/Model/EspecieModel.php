<?php 

namespace App\Model;

class EspecieModel {
    private $esp_id;
    private $nome_id

    public __get($nome) {
        return $this->$nome;
    }

    public __set($nome, $valor) {
        $this->$nome = $valor;
    }
}