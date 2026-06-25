<?php

namespace App\Controller;

use FW\Controller\Action;
use App\Model\OngModel;
use App\DAO\OngDAO;

class OngController extends Action {

    public function __construct() {
        parent::__construct();
        //$this->validaAutenticacao();
    }

    public function listar() {
        $this->getView()->title = 'ONGs';
        $this->getView()->title_pagina = 'Listar ONGs';

        $dao = new OngDAO();
        $this->getView()->lista_ongs = $dao->listar(); 

        $this->render('../dashboard/ong_listar', 'dashboard');
    }

    public function cadastro() {
        $this->getView()->title = 'Cadastro de ONG';
        $this->getView()->title_pagina = 'Cadastro de ONG';

        $this->render('../dashboard/ong_cadastro', 'dashboard');
    }

    public function salvar() {
        if (!empty($_POST)) {
            $ong = new OngModel();
            
            $ong->__set('ong_nome', $_POST['ong_nome'] ?? '');
            $ong->__set('ong_cnpj', $_POST['ong_cnpj'] ?? '');
            $ong->__set('ong_qnt_animais', $_POST['ong_qnt_animais'] ?? 0);
            $ong->__set('ong_cep', $_POST['ong_cep'] ?? '');
            $ong->__set('ong_estado', $_POST['ong_estado'] ?? '');
            $ong->__set('ong_cidade', $_POST['ong_cidade'] ?? '');
            $ong->__set('ong_bairro', $_POST['ong_bairro'] ?? '');
            $ong->__set('ong_logradouro', $_POST['ong_logradouro'] ?? '');
            $ong->__set('ong_numero', $_POST['ong_numero'] ?? '');
            $ong->__set('ong_complemento', $_POST['ong_complemento'] ?? '');
            $ong->__set('ong_tel1', $_POST['ong_tel1'] ?? '');
            $ong->__set('ong_tel2', $_POST['ong_tel2'] ?? '');
            $ong->__set('ong_status', $_POST['ong_status'] ?? 1);

            try {
                $dao = new OngDAO();
                $dao->inserir($ong);
                
                // Se salvou com sucesso, limpa qualquer erro antigo e vai para a listagem
                if (session_status() === PHP_SESSION_NONE) { session_start(); }
                unset($_SESSION['erro_cadastro_ong']);
                
                header('Location: /dashboard/ong/listar');
                die();
                
            } catch (\Exception $e) {
                // Captura o erro de CNPJ duplicado do DAO e joga na sessão
                if (session_status() === PHP_SESSION_NONE) { session_start(); }
                $_SESSION['erro_cadastro_ong'] = $e->getMessage();
                
                // Redireciona de volta para o formulário para exibir o alerta
                header('Location: /dashboard/ong/cadastro');
                die();
            }
        }

        header('Location: /dashboard/ong/listar');
        die();
    }

    public function editar($params) {
        $this->getView()->title = 'Editar ONG';
        $this->getView()->title_pagina = 'Editar ONG';
        $this->getView()->params = $params;

        $id = $params['id'] ?? $_GET['id'] ?? null;

        if ($id) {
            $dao = new OngDAO();
            $this->getView()->ong = $dao->buscarPorId($id);
        }

        $this->render('../dashboard/ong_editar', 'dashboard');
    }

    public function alterar() {
        if (!empty($_POST) && isset($_POST['ong_id'])) {
            $ong = new OngModel();
            
            $ong->__set('ong_id', $_POST['ong_id']);
            $ong->__set('ong_nome', $_POST['ong_nome'] ?? '');
            $ong->__set('ong_cnpj', $_POST['ong_cnpj'] ?? '');
            $ong->__set('ong_qnt_animais', $_POST['ong_qnt_animais'] ?? 0);
            $ong->__set('ong_cep', $_POST['ong_cep'] ?? '');
            $ong->__set('ong_estado', $_POST['ong_estado'] ?? '');
            $ong->__set('ong_cidade', $_POST['ong_cidade'] ?? '');
            $ong->__set('ong_bairro', $_POST['ong_bairro'] ?? '');
            $ong->__set('ong_logradouro', $_POST['ong_logradouro'] ?? '');
            $ong->__set('ong_numero', $_POST['ong_numero'] ?? '');
            $ong->__set('ong_complemento', $_POST['ong_complemento'] ?? '');
            $ong->__set('ong_tel1', $_POST['ong_tel1'] ?? '');
            $ong->__set('ong_tel2', $_POST['ong_tel2'] ?? '');
            $ong->__set('ong_status', $_POST['ong_status'] ?? 1);

            $dao = new OngDAO();
            $dao->alterar($ong);
        }

        header('Location: /dashboard/ong/listar');
        die();
    }

    public function excluir() {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $dao = new OngDAO();
            $dao->excluir($id);
        }

        header('Location: /dashboard/ong/listar');
        die();
    }

    public function validaAutenticacao() {
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: ../login');
            die();
        }
    }
}