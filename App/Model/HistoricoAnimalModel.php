<?php 

namespace App\Model;

class HistoricoAnimalModel {
    
    private $hist_id;
    private $hist_descr;
    private $hist_data;
    private $hist_tipo;
    private $fk_animal_id;
    private $fk_ong_id;
    private $fk_veterinario_id;

    public function __get($nome) {
        return $this->$nome;
    }

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }
    
}