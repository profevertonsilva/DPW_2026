<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\AdministradorDAO;
use App\Model\AdministradorModel;

class AdministradorController extends Action
{
    public function listar()
    {
        $dao = new AdministradorDAO();
        $administradores = $dao->listar();

        $this->getView()->title            = 'Administradores';
        $this->getView()->title_pagina     = 'Listar Administradores';
        $this->getView()->administradores  = $administradores;

        $this->render('../dashboard/administrador_listar', 'dashboard');
    }

    public function cadastro()
    {
        $this->getView()->title         = 'Cadastro de Administrador';
        $this->getView()->title_pagina  = 'Cadastro de Administrador';

        $this->render('../dashboard/administrador_cadastro', 'dashboard');
    }

    public function cadastrar()
    {
        $model = new AdministradorModel();
        $model->__set('adm_nome',        $_POST['nome']            ?? '');
        $model->__set('adm_cpf',         $_POST['cpf']             ?? '');
        $model->__set('adm_cep',         $_POST['cep']             ?? '');
        $model->__set('adm_logradouro',  $_POST['logradouro']      ?? '');
        $model->__set('adm_estado',      $_POST['estado']          ?? '');
        $model->__set('adm_complemento', $_POST['complemento']     ?? '');
        $model->__set('adm_dn',          $_POST['data_nascimento'] ?? null);
        $model->__set('adm_cidade',      $_POST['cidade']          ?? '');
        $model->__set('adm_bairro',      $_POST['bairro']          ?? '');
        $model->__set('adm_numero',      $_POST['numero']          ?? '');
        $model->__set('adm_tel1',        $_POST['telefone']        ?? '');
        $model->__set('adm_tel2',        $_POST['telefone_2']      ?? '');

        $dao = new AdministradorDAO();
        $dao->inserir($model);

        header('Location: /dashboard/administrador/listar');
        die();
    }

    public function editar($params)
    {
        $id  = $params['id'] ?? ($params[0] ?? null);
        $dao = new AdministradorDAO();
        $administrador = $dao->buscarPorId($id);

        $this->getView()->title            = 'Editar Administrador';
        $this->getView()->title_pagina     = 'Editar Administrador';
        $this->getView()->administrador    = $administrador;

        $this->render('../dashboard/administrador_editar', 'dashboard');
    }

    public function alterar()
    {
        $model = new AdministradorModel();
        $model->__set('adm_id',          $_POST['id']              ?? null);
        $model->__set('adm_nome',        $_POST['nome']            ?? '');
        $model->__set('adm_cpf',         $_POST['cpf']             ?? '');
        $model->__set('adm_cep',         $_POST['cep']             ?? '');
        $model->__set('adm_logradouro',  $_POST['logradouro']      ?? '');
        $model->__set('adm_estado',      $_POST['estado']          ?? '');
        $model->__set('adm_complemento', $_POST['complemento']     ?? '');
        $model->__set('adm_dn',          $_POST['data_nascimento'] ?? null);
        $model->__set('adm_cidade',      $_POST['cidade']          ?? '');
        $model->__set('adm_bairro',      $_POST['bairro']          ?? '');
        $model->__set('adm_numero',      $_POST['numero']          ?? '');
        $model->__set('adm_tel1',        $_POST['telefone']        ?? '');
        $model->__set('adm_tel2',        $_POST['telefone_2']      ?? '');

        $dao = new AdministradorDAO();
        $dao->alterar($model);

        header('Location: /dashboard/administrador/listar');
        die();
    }

    public function excluir()
    {
        $id  = $_POST['id'] ?? null;
        $dao = new AdministradorDAO();
        $dao->excluir($id);

        header('Location: /dashboard/administrador/listar');
        die();
    }

    public function promoverUsuario()
    {
        $dao = new AdministradorDAO();
        $usuarios = $dao->listarUsuariosParaPromover();

        $this->getView()->title         = 'Promover Usuário a Administrador';
        $this->getView()->title_pagina  = 'Promover Usuário a Administrador';
        $this->getView()->usuarios      = $usuarios;

        $this->render('../dashboard/administrador_promover', 'dashboard');
    }

    public function promover()
    {
        $loginId = $_POST['login_id'] ?? null;
        $dao = new AdministradorDAO();
        $dao->promoverUsuario($loginId);

        header('Location: /dashboard/administrador/promover-usuario');
        die();
    }

    public function validaAutenticacao()
    {
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /login');
            die();
        }
    }
}
