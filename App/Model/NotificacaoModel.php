<?php
class Notificacao
{
    private $id;
    private $fk_login_id;
    private $titulo;
    private $mensagem;
    private $data;
    private $lida;
    private $tipo;
    private $destino_tab;
    private $destino_tela;
    private $destino_params;

    public function __set($nome,$valor)
    {
        $this->$nome = $valor;
    }

    public function __get($nome)
    {
        return $this->$nome;
    }
}

?>