<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\OngDAO;
use App\Model\OngModel;

class OngController extends Action {

    public function listar() {

        $dao = new OngDAO();

        $this->getView()->ongs = $dao->listar();

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

        $ong = new OngModel();

        $ong->__set('ong_nome', $_POST['ong_nome']);
        $ong->__set('ong_cnpj', $_POST['ong_cnpj']);
        $ong->__set('ong_qnt_animais', $_POST['ong_qnt_animais']);
        $ong->__set('ong_cep', $_POST['ong_cep']);
        $ong->__set('ong_estado', $_POST['ong_estado']);
        $ong->__set('ong_cidade', $_POST['ong_cidade']);
        $ong->__set('ong_bairro', $_POST['ong_bairro']);
        $ong->__set('ong_logradouro', $_POST['ong_logradouro']);
        $ong->__set('ong_numero', $_POST['ong_numero']);
        $ong->__set('ong_complemento', $_POST['ong_complemento']);
        $ong->__set('ong_tel1', $_POST['ong_tel1']);
        $ong->__set('ong_tel2', $_POST['ong_tel2']);
        $ong->__set('ong_status', $_POST['ong_status']);

        $dao = new OngDAO();
        $dao->inserir($ong);

        header('Location: /dashboard/ong/listar');
        die();
    }

    public function editar($params) {

        $dao = new OngDAO();

        $this->getView()->ong = $dao->buscarPorId($params['id']);

        $this->getView()->title = 'Editar ONG';
        $this->getView()->title_pagina = 'Editar ONG';
        $this->getView()->params = $params;

        $this->render('../dashboard/ong_editar', 'dashboard');
    }

    public function alterar() {

        $ong = new OngModel();

        $ong->__set('ong_id', $_POST['ong_id']);
        $ong->__set('ong_nome', $_POST['ong_nome']);
        $ong->__set('ong_cnpj', $_POST['ong_cnpj']);
        $ong->__set('ong_qnt_animais', $_POST['ong_qnt_animais']);
        $ong->__set('ong_cep', $_POST['ong_cep']);
        $ong->__set('ong_estado', $_POST['ong_estado']);
        $ong->__set('ong_cidade', $_POST['ong_cidade']);
        $ong->__set('ong_bairro', $_POST['ong_bairro']);
        $ong->__set('ong_logradouro', $_POST['ong_logradouro']);
        $ong->__set('ong_numero', $_POST['ong_numero']);
        $ong->__set('ong_complemento', $_POST['ong_complemento']);
        $ong->__set('ong_tel1', $_POST['ong_tel1']);
        $ong->__set('ong_tel2', $_POST['ong_tel2']);
        $ong->__set('ong_status', $_POST['ong_status']);

        $dao = new OngDAO();
        $dao->alterar($ong);

        header('Location: /dashboard/ong/listar');
        die();
    }

    public function excluir() {

        $dao = new OngDAO();

        $dao->excluir($_POST['id']);

        header('Location: /dashboard/ong/listar');
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