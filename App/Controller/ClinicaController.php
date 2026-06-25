<?php

namespace App\Controller;

use FW\Controller\Action;
use App\Model\ClinicaModel;
use App\DAO\ClinicaDAO; // Importação do DAO ativada

class ClinicaController extends Action {

    public function __construct() {
        parent::__construct();
        //$this->validaAutenticacao();
    }

    public function listar() {
        $this->getView()->title = 'Clínicas';
        $this->getView()->title_pagina = 'Listar Clínicas';

        // Instancia o DAO e busca a lista real do banco de dados
        $dao = new ClinicaDAO();
        $this->getView()->lista_clinicas = $dao->listar(); 

        $this->render('../dashboard/clinica_listar', 'dashboard');
    }

    public function cadastro() {
        $this->getView()->title = 'Cadastro de Clínica';
        $this->getView()->title_pagina = 'Cadastro de Clínica';

        $this->render('../dashboard/clinica_cadastro', 'dashboard');
    }

    public function cadastrar() {
        if (!empty($_POST)) {
            $clinica = new ClinicaModel();
            
            $clinica->__set('cln_nome', $_POST['cln_nome'] ?? '');
            $clinica->__set('cln_cnpj', $_POST['cln_cnpj'] ?? '');
            $clinica->__set('cln_cep', $_POST['cln_cep'] ?? '');
            $clinica->__set('cln_estado', $_POST['cln_estado'] ?? '');
            $clinica->__set('cln_cidade', $_POST['cln_cidade'] ?? '');
            $clinica->__set('cln_bairro', $_POST['cln_bairro'] ?? '');
            $clinica->__set('cln_logradouro', $_POST['cln_logradouro'] ?? '');
            $clinica->__set('cln_numero', $_POST['cln_numero'] ?? '');
            $clinica->__set('cln_complemento', $_POST['cln_complemento'] ?? '');
            $clinica->__set('cln_tel1', $_POST['cln_tel1'] ?? '');
            $clinica->__set('cln_tel2', $_POST['cln_tel2'] ?? '');

            try {
                // Executa a inserção no banco de dados
                $dao = new ClinicaDAO();
                $dao->inserir($clinica);
                
                // Se salvou com sucesso, limpa qualquer resíduo de erro da sessão
                if (session_status() === PHP_SESSION_NONE) { session_start(); }
                unset($_SESSION['erro_cadastro_clinica']);

                // Redireciona para a listagem
                header('Location: /dashboard/clinica/listar');
                die();

            } catch (\Exception $e) {
                // Ativa a sessão se não estiver ativa
                if (session_status() === PHP_SESSION_NONE) { session_start(); }

                // Guarda a mensagem customizada do DAO na sessão
                $_SESSION['erro_cadastro_clinica'] = $e->getMessage();

                // Redireciona de volta para a tela de cadastro para exibir o alerta
                header('Location: /dashboard/clinica/cadastro');
                die();
            }
        }

        header('Location: /dashboard/clinica/listar');
        die();
    }

    public function editar($params) {
        $this->getView()->title = 'Editar Clínica';
        $this->getView()->title_pagina = 'Editar Clínica';
        $this->getView()->params = $params;

        // Tenta capturar o ID de forma flexível (pelo array de parâmetros do roteador ou pela URL via $_GET)
        $id = $params['id'] ?? $_GET['id'] ?? null;

        if ($id) {
            // Busca a clínica específica para preencher os campos da View
            $dao = new ClinicaDAO();
            $this->getView()->clinica = $dao->buscarPorId($id);
        }

        $this->render('../dashboard/clinica_editar', 'dashboard');
    }

    public function alterar() {
        if (!empty($_POST) && isset($_POST['cln_id'])) {
            $clinica = new ClinicaModel();
            
            $clinica->__set('cln_id', $_POST['cln_id']);
            $clinica->__set('cln_nome', $_POST['cln_nome'] ?? '');
            $clinica->__set('cln_cnpj', $_POST['cln_cnpj'] ?? '');
            $clinica->__set('cln_cep', $_POST['cln_cep'] ?? '');
            $clinica->__set('cln_estado', $_POST['cln_estado'] ?? '');
            $clinica->__set('cln_cidade', $_POST['cln_cidade'] ?? '');
            $clinica->__set('cln_bairro', $_POST['cln_bairro'] ?? '');
            $clinica->__set('cln_logradouro', $_POST['cln_logradouro'] ?? '');
            $clinica->__set('cln_numero', $_POST['cln_numero'] ?? '');
            $clinica->__set('cln_complemento', $_POST['cln_complemento'] ?? '');
            $clinica->__set('cln_tel1', $_POST['cln_tel1'] ?? '');
            $clinica->__set('cln_tel2', $_POST['cln_tel2'] ?? '');

            // Executa a atualização no banco de dados
            $dao = new ClinicaDAO();
            $dao->alterar($clinica);
        }

        // Estratégia relativa sem barras
        header('Location: /dashboard/clinica/listar');
        die();
    }

    public function excluir() {
        $id = $_GET['id'] ?? null;

        if ($id) {
            // Executa a exclusão física do registro
            $dao = new ClinicaDAO();
            $dao->excluir($id);
        }

        // Estratégia relativa sem barras
        header('Location: /dashboard/clinica/listar');
        die();
    }

    public function validaAutenticacao() {
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: ../login');
            die();
        }
    }
}