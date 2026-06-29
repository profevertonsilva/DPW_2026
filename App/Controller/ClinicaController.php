<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\ClinicaDAO;
use App\Model\ClinicaModel;

class ClinicaController extends Action {

    public function listar() {

        $dao = new ClinicaDAO();

        $this->getView()->clinicas = $dao->listar();

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

        $clinica = new ClinicaModel();

        $clinica->__set('cnpj', $_POST['cnpj']);
        $clinica->__set('nome', $_POST['nome']);
        $clinica->__set('cep', $_POST['cep']);
        $clinica->__set('estado', $_POST['estado']);
        $clinica->__set('bairro', $_POST['bairro']);
        $clinica->__set('logradouro', $_POST['logradouro']);
        $clinica->__set('cidade', $_POST['cidade']);
        $clinica->__set('numero', $_POST['numero']);
        $clinica->__set('complemento', $_POST['complemento']);
        $clinica->__set('tel1', $_POST['tel1']);
        $clinica->__set('tel2', $_POST['tel2']);

        $dao = new ClinicaDAO();

        $dao->inserir($clinica);

        header('Location: /dashboard/clinica/listar');
        die();
    }

    public function editar($params) {

        $dao = new ClinicaDAO();

        $this->getView()->clinica = $dao->buscarPorId($params['id']);

        $this->getView()->title = 'Editar Clínica';
        $this->getView()->title_pagina = 'Editar Clínica';
        $this->getView()->params = $params;

        $this->render('../dashboard/clinica_editar', 'dashboard');
    }

    public function alterar() {

        $clinica = new ClinicaModel();

        $clinica->__set('id', $_POST['id']);
        $clinica->__set('cnpj', $_POST['cnpj']);
        $clinica->__set('nome', $_POST['nome']);
        $clinica->__set('cep', $_POST['cep']);
        $clinica->__set('estado', $_POST['estado']);
        $clinica->__set('bairro', $_POST['bairro']);
        $clinica->__set('logradouro', $_POST['logradouro']);
        $clinica->__set('cidade', $_POST['cidade']);
        $clinica->__set('numero', $_POST['numero']);
        $clinica->__set('complemento', $_POST['complemento']);
        $clinica->__set('telefone_1', $_POST['telefone_1']);
        $clinica->__set('telefone_2', $_POST['telefone_2']);

        $dao = new ClinicaDAO();

        $dao->alterar($clinica);

        header('Location: /dashboard/clinica/listar');
        die();
    }

    public function excluir() {

        $dao = new ClinicaDAO();

        $dao->excluir($_POST['id']);

        header('Location: /dashboard/clinica/listar');
        die();
    }

    public function validaAutenticacao() {

        if (
            !isset($_SESSION['id']) ||
            $_SESSION['id'] == '' ||
            !isset($_SESSION['nome']) ||
            $_SESSION['nome'] == ''
        ) {

            header('Location: /login');
            die();
        }
    }
}

