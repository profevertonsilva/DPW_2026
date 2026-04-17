<?php

namespace App\Controller;

use FW\Controller\Action;

class ClinicaController extends Action {

    public function listar() {
        $this->getView()->title = 'Clínicas';
        $this->getView()->title_pagina = 'Listar Clínicas';

        $this->render('../dashboard/clinica_listar', 'dashboard');
    }

    public function cadastro() {
        $this->getView()->title = 'Cadastro de Clínica';
        $this->getView()->title_pagina = 'Cadastro de Clínica';

        $this->render('../dashboard/clinica_cadastro', 'dashboard');
    }

    public function cadastrar() {
        // TODO: implementar inserção de clínica no banco
        header('Location: /dashboard/clinica/listar');
        die();
    }

    public function editar($params) {
        $this->getView()->title = 'Editar Clínica';
        $this->getView()->title_pagina = 'Editar Clínica';
        $this->getView()->params = $params;

        $this->render('../dashboard/clinica_editar', 'dashboard');
    }

    public function alterar() {
        // TODO: implementar alteração de clínica no banco
        header('Location: /dashboard/clinica/listar');
        die();
    }

    public function excluir() {
        // TODO: implementar exclusão de clínica no banco
        header('Location: /dashboard/clinica/listar');
        die();
    }

    public function validaAutenticacao() {
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /login');
            die();
        }
    }
}
