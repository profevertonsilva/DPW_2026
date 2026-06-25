<?php

namespace App\Controller;

use FW\Controller\Action;
use App\Model\VeterinarioModel;
use App\DAO\VeterinarioDAO;

class VeterinarioController extends Action {

    public function __construct() {
        parent::__construct();
        //$this->validaAutenticacao();
    }

    public function listar() {
        $this->getView()->title = 'Veterinários';
        $this->getView()->title_pagina = 'Listar Veterinários';

        $dao = new VeterinarioDAO();
        $this->getView()->lista_veterinarios = $dao->listar(); 

        $this->render('../dashboard/veterinario_listar', 'dashboard');
    }

    public function cadastro() {
        $this->getView()->title = 'Cadastro de Veterinário';
        $this->getView()->title_pagina = 'Cadastro de Veterinário';

        $this->render('../dashboard/veterinario_cadastro', 'dashboard');
    }

    public function cadastrar() {
        if (!empty($_POST)) {
            $vet = new VeterinarioModel();
            
            $vet->__set('vet_nome', $_POST['vet_nome'] ?? '');
            $vet->__set('vet_cpf', $_POST['vet_cpf'] ?? '');
            $vet->__set('vet_crmv', $_POST['vet_crmv'] ?? '');
            $vet->__set('vet_dn', $_POST['vet_dn'] ?? null);
            $vet->__set('vet_cep', $_POST['vet_cep'] ?? '');
            $vet->__set('vet_estado', $_POST['vet_estado'] ?? '');
            $vet->__set('vet_cidade', $_POST['vet_cidade'] ?? '');
            $vet->__set('vet_bairro', $_POST['vet_bairro'] ?? '');
            $vet->__set('vet_logradouro', $_POST['vet_logradouro'] ?? '');
            $vet->__set('vet_numero', $_POST['vet_numero'] ?? '');
            $vet->__set('vet_complemento', $_POST['vet_complemento'] ?? '');
            $vet->__set('vet_tel1', $_POST['vet_tel1'] ?? '');
            $vet->__set('vet_tel2', $_POST['vet_tel2'] ?? '');

            try {
                $dao = new VeterinarioDAO();
                $dao->inserir($vet);
                
                if (session_status() === PHP_SESSION_NONE) { session_start(); }
                unset($_SESSION['erro_cadastro_veterinario']);

                // Redirecionamento limpo pós-sucesso usando a URL correta mapeada nas routes
                echo "<script>window.location.href = '/dashboard/veterinario/listar';</script>";
                exit();
            } catch (\Exception $e) {
                if (session_status() === PHP_SESSION_NONE) { session_start(); }
                $_SESSION['erro_cadastro_veterinario'] = $e->getMessage();
                
                // Passa o model preenchido de volta para repopulate
                $this->getView()->veterinario = $vet;

                // CORREÇÃO: Em vez de header(), renderiza direto para não perder o objeto $this->getView()
                $this->getView()->title = 'Cadastro de Veterinário';
                $this->getView()->title_pagina = 'Cadastro de Veterinário';
                $this->render('../dashboard/veterinario_cadastro', 'dashboard');
                exit();
            }
        }

        header('Location: /dashboard/veterinario/listar');
        die();
    }

    public function editar($params) {
        $this->getView()->title = 'Editar Veterinário';
        $this->getView()->title_pagina = 'Editar Veterinário';
        $this->getView()->params = $params;

        $id = $params['id'] ?? $_GET['id'] ?? null;

        if ($id) {
            $dao = new VeterinarioDAO();
            $this->getView()->veterinario = $dao->buscarPorId($id);
        }

        $this->render('../dashboard/veterinario_editar', 'dashboard');
    }

    public function alterar() {
        if (!empty($_POST) && isset($_POST['vet_id'])) {
            $vet = new VeterinarioModel();
            
            $vet->__set('vet_id', $_POST['vet_id']);
            $vet->__set('vet_nome', $_POST['vet_nome'] ?? '');
            $vet->__set('vet_cpf', $_POST['vet_cpf'] ?? '');
            $vet->__set('vet_crmv', $_POST['vet_crmv'] ?? '');
            $vet->__set('vet_dn', $_POST['vet_dn'] ?? null);
            $vet->__set('vet_cep', $_POST['vet_cep'] ?? '');
            $vet->__set('vet_estado', $_POST['vet_estado'] ?? '');
            $vet->__set('vet_cidade', $_POST['vet_cidade'] ?? '');
            $vet->__set('vet_bairro', $_POST['vet_bairro'] ?? '');
            $vet->__set('vet_logradouro', $_POST['vet_logradouro'] ?? '');
            $vet->__set('vet_numero', $_POST['vet_numero'] ?? '');
            $vet->__set('vet_complemento', $_POST['vet_complemento'] ?? '');
            $vet->__set('vet_tel1', $_POST['vet_tel1'] ?? '');
            $vet->__set('vet_tel2', $_POST['vet_tel2'] ?? '');

            $dao = new VeterinarioDAO();
            $dao->alterar($vet);
        }

        header('Location: /dashboard/veterinario/listar');
        die();
    }

    public function excluir() {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $dao = new VeterinarioDAO();
            $dao->excluir($id);
        }

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