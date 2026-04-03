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

    public __get($nome) {
        return $this->$nome;
    }

    public __set($nome, $valor) {
        $this->$nome = $valor;
    }
    
}