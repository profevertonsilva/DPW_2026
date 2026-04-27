<?php
/*
 * @Author marcus rito
 */


namespace App\Model;

class AnimalModel
{

    private $ani_id;
    private $ani_nome;
    private $ani_sexo;
    private $ani_data_nascimento;

    // Inserção dos novos campos - 27/04/2026
    private $ani_cor;
    private $ani_porte;
    private $ani_status;
    private $ani_castrado;
    private $ani_foto;
    private $ani_descricao;
    private $ani_fk_especie_id;


    public function __get($nome)
    {
        return $this->$nome;
    }

    public function __set($nome, $valor)
    {
        $this->$nome = $valor;
    }
}
