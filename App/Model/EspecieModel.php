<?php 

namespace App\Model;

class EspecieModel {
    private $esp_id;
    private $nome_id;

    public function __get($nome) {
        return $this->$nome;
    }

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }
}