<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\NotificacaoDAO;

class NotificacaoController extends Action
{
    public function listar()
    {

     $_SESSION['id'] = 1; 
        //1. Salvando temporariamente o id do usuário na sessão para teste. Remover depois. 
        $dao = new NotificacaoDAO();
        $this->validaAutenticacao();

        $this->getView()->notificacoes =
            $dao->listarPorUsuario($_SESSION['id']);

        $this->getView()->title = "Notificações";
        $this->getView()->title_pagina = "Notificações";

        $this->render('../dashboard/notificacao_listar', 'dashboard');
    }

    public function marcarComoLida()
    {
        $this->validaAutenticacao();

        $id = $_POST['id'] ?? null;

        if ($id) {

            $dao = new NotificacaoDAO();
            $dao->marcarComoLida($id);

        }

        header('Location: /dashboard/notificacao/listar');
        die();
    }

    public function contarNaoLidas()
    {
        $this->validaAutenticacao();

        $dao = new NotificacaoDAO();

        echo $dao->contarNaoLidas($_SESSION['id']);
    }

    public function validaAutenticacao()
{
    if (!isset($_SESSION['id'])) {
        header('Location: /login');
        die();
    }
}
}
