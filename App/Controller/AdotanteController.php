<?php

namespace App\Controller;

use App\DAO\AdotanteDAO;
use App\Model\AdotanteModel;

class AdotanteController extends \FW\Controller\Action

{
    public function listar()
    {
        $dao = new AdotanteDAO();
        $adotantes = $dao->listar();

        $this->getView()->title       = 'Adotantes';
        $this->getView()->title_pagina = 'Listar Adotantes';
        $this->getView()->adotantes   = $adotantes;

        $this->render('../dashboard/adotante_listar', 'dashboard');
    }

    public function cadastro()
    {
        // Se houver dados via POST, o Controller intercepta e tenta salvar
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || !empty($_POST)) {
            $model = new AdotanteModel();
            
            // Popula a model com os dados vindos do formulário
            $model->__set('nome',            $_POST['nome']            ?? '');
            $model->__set('cpf',             $_POST['cpf']             ?? '');
            $model->__set('data_nascimento', $_POST['data_nascimento'] ?? null);
            $model->__set('cep',             $_POST['cep']             ?? '');
            $model->__set('estado',          $_POST['estado']          ?? '');
            $model->__set('cidade',          $_POST['cidade']          ?? '');
            $model->__set('bairro',          $_POST['bairro']          ?? '');
            $model->__set('logradouro',      $_POST['logradouro']      ?? '');
            $model->__set('numero',          $_POST['numero']          ?? '');
            $model->__set('complemento',     $_POST['complemento']     ?? '');
            $model->__set('telefone_1',      $_POST['telefone_1']      ?? '');
            $model->__set('telefone_2',      $_POST['telefone_2']      ?? '');
            $model->__set('status',          $_POST['status']          ?? 'bom');
            $model->__set('fk_login_id', 1);

            try {
                $dao = new AdotanteDAO();
                $dao->inserir($model);

                // Saída limpa para evitar quebras com o layout padrão do professor
                echo "<script>window.location.href = '/dashboard/adotante/listar';</script>";
                exit();
            } catch (\Exception $e) {
                // Inicia a sessão se ainda não tiver sido iniciada
                if (session_status() === PHP_SESSION_NONE) { 
                    session_start(); 
                }
                
                // Grava o erro capturado no DAO (Ex: "Este CPF já está cadastrado no sistema.")
                $_SESSION['erro_cadastro_adotante'] = $e->getMessage();
                
                // Passa o objeto atual para a View manter o "repopulate" (preservar campos digitados)
                // Usamos os getters/setters dinâmicos esperados pela sua view (Ex: adt_nome, adt_cpf)
                $adotanteRepopulate = new \stdClass();
                $adotanteRepopulate->adt_nome = $model->__get('nome');
                $adotanteRepopulate->adt_cpf = $model->__get('cpf');
                $adotanteRepopulate->adt_dn = $model->__get('data_nascimento');
                $adotanteRepopulate->adt_tel1 = $model->__get('telefone_1');
                $adotanteRepopulate->adt_tel2 = $model->__get('telefone_2');
                $adotanteRepopulate->adt_cep = $model->__get('cep');
                $adotanteRepopulate->adt_logradouro = $model->__get('logradouro');
                $adotanteRepopulate->adt_numero = $model->__get('numero');
                $adotanteRepopulate->adt_complemento = $model->__get('complemento');
                $adotanteRepopulate->adt_bairro = $model->__get('bairro');
                $adotanteRepopulate->adt_cidade = $model->__get('cidade');
                $adotanteRepopulate->adt_estado = $model->__get('estado');

                // Envia para o formulário se repopular
                $this->getView()->adotante = $adotanteRepopulate;
            }
        }

        // Se for acesso normal (GET) ou se caiu no catch de erro, renderiza a tela
        $this->getView()->title       = 'Cadastro de Adotante';
        $this->getView()->title_pagina = 'Cadastro de Adotante';

        $this->render('../dashboard/adotante_cadastro', 'dashboard');
    }

    public function cadastrar()
    {
        // Garante suporte se o roteador chamar o método no infinitivo
        $this->cadastro();
    }

    public function editar($params = null)
    {
        $id  = $params['id'] ?? ($params[0] ?? null);
        $dao = new AdotanteDAO();
        $adotante = $dao->buscarPorId($id);

        $this->getView()->title        = 'Editar Adotante';
        $this->getView()->title_pagina = 'Editar Adotante';
        $this->getView()->adotante     = $adotante;

        $this->render('../dashboard/adotante_editar', 'dashboard');
    }

    public function alterar()
    {
        $model = new AdotanteModel();
        $model->__set('id',   $_POST['id']             ?? null);
        $model->__set('nome', $_POST['nome']            ?? '');
        $model->__set('cpf',  $_POST['cpf']             ?? '');
        $model->__set('data_nascimento',   $_POST['data_nascimento'] ?? null);
        $model->__set('cep',  $_POST['cep']             ?? '');
        $model->__set('estado', $_POST['estado']        ?? '');
        $model->__set('cidade', $_POST['cidade']        ?? '');
        $model->__set('bairro', $_POST['bairro']        ?? '');
        $model->__set('logradouro',  $_POST['logradouro']  ?? '');
        $model->__set('numero',      $_POST['numero']      ?? '');
        $model->__set('complemento', $_POST['complemento'] ?? '');
        $model->__set('telefone_1',   $_POST['telefone_1'] ?? '');
        $model->__set('telefone_2',   $_POST['telefone_2'] ?? '');
        $model->__set('status', $_POST['status']     ?? 'bom');

        $dao = new AdotanteDAO();
        $dao->alterar($model);

        header('Location: /dashboard/adotante/listar');
        die();
    }

    public function excluir()
    {
        $id  = $_POST['id'] ?? null;
        $dao = new AdotanteDAO();
        $dao->excluir($id);

        header('Location: /dashboard/adotante/listar');
        die();
    }

    public function validaAutenticacao()
    {
    }
}