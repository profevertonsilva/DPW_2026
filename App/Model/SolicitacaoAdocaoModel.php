<?php 

namespace App\Model;

class SolicitacaoAdocaoModel {

    private $solAdc_id;
    private $solAdc_data;
    private $solAdc_status;
    private $solAdc_motivo;
    private $fk_adotante_id;
    private $fk_animal_id;

    public function __get($nome) {
        return $this->$nome;
    }

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }

}