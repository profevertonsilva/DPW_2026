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

        $clinica->__set('cln_cnpj', $_POST['cln_cnpj']);
        $clinica->__set('cln_nome', $_POST['cln_nome']);
        $clinica->__set('cln_cep', $_POST['cln_cep']);
        $clinica->__set('cln_estado', $_POST['cln_estado']);
        $clinica->__set('cln_bairro', $_POST['cln_bairro']);
        $clinica->__set('cln_logradouro', $_POST['cln_logradouro']);
        $clinica->__set('cln_cidade', $_POST['cln_cidade']);
        $clinica->__set('cln_numero', $_POST['cln_numero']);
        $clinica->__set('cln_complemento', $_POST['cln_complemento']);
        $clinica->__set('cln_tel1', $_POST['cln_tel1']);
        $clinica->__set('cln_tel2', $_POST['cln_tel2']);

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

        $clinica->__set('cln_id', $_POST['cln_id']);
        $clinica->__set('cln_cnpj', $_POST['cln_cnpj']);
        $clinica->__set('cln_nome', $_POST['cln_nome']);
        $clinica->__set('cln_cep', $_POST['cln_cep']);
        $clinica->__set('cln_estado', $_POST['cln_estado']);
        $clinica->__set('cln_bairro', $_POST['cln_bairro']);
        $clinica->__set('cln_logradouro', $_POST['cln_logradouro']);
        $clinica->__set('cln_cidade', $_POST['cln_cidade']);
        $clinica->__set('cln_numero', $_POST['cln_numero']);
        $clinica->__set('cln_complemento', $_POST['cln_complemento']);
        $clinica->__set('cln_tel1', $_POST['cln_tel1']);
        $clinica->__set('cln_tel2', $_POST['cln_tel2']);

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

