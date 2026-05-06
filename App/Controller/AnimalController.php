<?php

namespace App\Controller;

use FW\Controller\Action;

class AnimalController extends Action {

    public function listar() {
        $this->getView()->title = 'Animais';
        $this->getView()->title_pagina = 'Listar Animais';

        $this->render('../dashboard/animal_listar', 'dashboard');
    }

    public function cadastro() {
        $this->getView()->title = 'Cadastro de Animal';
        $this->getView()->title_pagina = 'Cadastro de Animal';

        $this->render('../dashboard/animal_cadastro', 'dashboard');
    }

    public function cadastrar() {
        // TODO: implementar inserção de animal no banco
        header('Location: /dashboard/animal/listar');
        die();
    }

    public function editar($params) {
        $this->getView()->title = 'Editar Animal';
        $this->getView()->title_pagina = 'Editar Animal';
        $this->getView()->params = $params;

        $this->render('../dashboard/animal_editar', 'dashboard');
    }

    public function alterar() {
        // TODO: implementar alteração de animal no banco
        header('Location: /dashboard/animal/listar');
        die();
    }

    public function excluir() {
        // TODO: implementar exclusão de animal no banco
        header('Location: /dashboard/animal/listar');
        die();
    }

    public function validaAutenticacao() {
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /login');
            die();
        }
    }
}
