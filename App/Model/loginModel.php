<?php 

namespace App\Model;

class LoginModel {

    private $lgn_id;
    private $lgn_data_cadastro;
    private $lgn_tipo_usuario;
    private $lgn_email;
    private $lgn_senha;
    private $lgn_status;
    private $fk_adotante_id;
    private $fk_rastreador_id;
    private $fk_ong_id;
    private $fk_administrador_id;
    private $fk_veterinario_id;

    public __get($nome) {
        return $this->$nome;
    }

    public __set($nome, $valor) {
        $this->$nome = $valor;
    }

}