<?php

namespace App\Controller;

use FW\Controller\Action;

class OngController extends Action {

    public function listar() {
        $this->getView()->title = 'ONGs';
        $this->getView()->title_pagina = 'Listar ONGs';

        $this->render('../dashboard/ong_listar', 'dashboard');
    }

    public function cadastro() {
        $this->getView()->title = 'Cadastro de ONG';
        $this->getView()->title_pagina = 'Cadastro de ONG';

        $this->render('../dashboard/ong_cadastro', 'dashboard');
    }

    public function cadastrar() {
        // TODO: implementar inserção de ONG no banco
        header('Location: /dashboard/ong/listar');
        die();
    }

    public function editar($params) {
        $this->getView()->title = 'Editar ONG';
        $this->getView()->title_pagina = 'Editar ONG';
        $this->getView()->params = $params;

        $this->render('../dashboard/ong_editar', 'dashboard');
    }

    public function alterar() {
        // TODO: implementar alteração de ONG no banco
        header('Location: /dashboard/ong/listar');
        die();
    }

    public function excluir() {
        // TODO: implementar exclusão de ONG no banco
        header('Location: /dashboard/ong/listar');
        die();
    }

    public function validaAutenticacao() {
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /login');
            die();
        }
    }
}
