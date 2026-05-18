<?php
/*
 * @Author marcus rito
 */


namespace App\Model;

class AnimalModel
{

    private $id;
    private $nome;
    private $sexo;
    private $data_nascimento;

    // Inserção dos novos campos - 27/04/2026
    private $cor;
    private $porte;
    private $status;
    private $castrado;
    private $foto;
    private $descricao;

    public function __get($nome)
    {
        return $this->$nome;
    }

    public function __set($nome, $valor)
    {
        $this->$nome = $valor;
    }
}
