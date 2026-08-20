<?php 

namespace App\Model;

class LoginModel {

    private $id;
    private $email;
    private $senha;
    private $status;
    private $tipo_usuario;
    private $data_cadastro;
    private $data_atualizacao;

    public function __get($nome) {
        return $this->$nome;
    }

    public function __set($nome, $valor) {
        $this->$nome = $valor;
    }

}