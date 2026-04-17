<?php

namespace App\Controller;

use FW\Controller\Action;

class VeterinarioController extends Action {

    public function listar() {
        $this->getView()->title = 'Veterinários';
        $this->getView()->title_pagina = 'Listar Veterinários';

        $this->render('../dashboard/veterinario_listar', 'dashboard');
    }

    public function cadastro() {
        $this->getView()->title = 'Cadastro de Veterinário';
        $this->getView()->title_pagina = 'Cadastro de Veterinário';

        $this->render('../dashboard/veterinario_cadastro', 'dashboard');
    }

    public function cadastrar() {
        // TODO: implementar inserção de veterinário no banco
        header('Location: /dashboard/veterinario/listar');
        die();
    }

    public function editar($params) {
        $this->getView()->title = 'Editar Veterinário';
        $this->getView()->title_pagina = 'Editar Veterinário';
        $this->getView()->params = $params;

        $this->render('../dashboard/veterinario_editar', 'dashboard');
    }

    public function alterar() {
        // TODO: implementar alteração de veterinário no banco
        header('Location: /dashboard/veterinario/listar');
        die();
    }

    public function excluir() {
        // TODO: implementar exclusão de veterinário no banco
        header('Location: /dashboard/veterinario/listar');
        die();
    }

    public function validaAutenticacao() {
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /login');
            die();
        }
    }
}
