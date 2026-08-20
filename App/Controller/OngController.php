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

        $ong->__set('nome', $_POST['nome']);
        $ong->__set('cnpj', $_POST['cnpj']);
        $ong->__set('qnt_animais', $_POST['qnt_animais']);
        $ong->__set('cep', $_POST['cep']);
        $ong->__set('estado', $_POST['estado']);
        $ong->__set('cidade', $_POST['cidade']);
        $ong->__set('bairro', $_POST['bairro']);
        $ong->__set('logradouro', $_POST['logradouro']);
        $ong->__set('numero', $_POST['numero']);
        $ong->__set('complemento', $_POST['complemento']);
        $ong->__set('telefone_1', $_POST['telefone_1']);
        $ong->__set('telefone_2', $_POST['telefone_2']);
        $ong->__set('status', $_POST['status']);

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

        $ong->__set('id', $_POST['id']);
        $ong->__set('nome', $_POST['nome']);
        $ong->__set('cnpj', $_POST['cnpj']);
        $ong->__set('qnt_animais', $_POST['qnt_animais']);
        $ong->__set('cep', $_POST['cep']);
        $ong->__set('estado', $_POST['estado']);
        $ong->__set('cidade', $_POST['cidade']);
        $ong->__set('bairro', $_POST['bairro']);
        $ong->__set('logradouro', $_POST['logradouro']);
        $ong->__set('numero', $_POST['numero']);
        $ong->__set('complemento', $_POST['complemento']);
        $ong->__set('tel1', $_POST['tel1']);
        $ong->__set('tel2', $_POST['tel2']);
        $ong->__set('status', $_POST['status']);

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