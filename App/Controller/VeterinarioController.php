<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\VeterinarioDAO;
use App\Model\VeterinarioModel;

class VeterinarioController extends Action
{
    public function listar()
    {
        $dao = new VeterinarioDAO();
        $veterinarios = $dao->listar();

        $this->getView()->title = 'Veterinários';
        $this->getView()->title_pagina = 'Listar Veterinários';
        $this->getView()->veterinarios = $veterinarios;

        $this->render('../dashboard/veterinario_listar', 'dashboard');
    }

    public function cadastro()
    {
        $this->getView()->title = 'Cadastro de Veterinário';
        $this->getView()->title_pagina = 'Cadastro de Veterinário';

        $this->render('../dashboard/veterinario_cadastro', 'dashboard');
    }

    public function cadastrar()
    {
        $model = new VeterinarioModel();

        $model->__set('vet_nome', $_POST['vet_nome'] ?? '');
        $model->__set('vet_cpf', $_POST['vet_cpf'] ?? '');
        $model->__set('vet_crmv', $_POST['vet_crmv'] ?? '');
        $model->__set('vet_dn', $_POST['vet_dn'] ?? null);

        $model->__set('vet_cep', $_POST['vet_cep'] ?? '');
        $model->__set('vet_estado', $_POST['vet_estado'] ?? '');
        $model->__set('vet_cidade', $_POST['vet_cidade'] ?? '');
        $model->__set('vet_bairro', $_POST['vet_bairro'] ?? '');
        $model->__set('vet_logradouro', $_POST['vet_logradouro'] ?? '');
        $model->__set('vet_numero', $_POST['vet_numero'] ?? '');
        $model->__set('vet_complemento', $_POST['vet_complemento'] ?? '');

        $model->__set('vet_tel1', $_POST['vet_tel1'] ?? '');
        $model->__set('vet_tel2', $_POST['vet_tel2'] ?? '');

        $dao = new VeterinarioDAO();
        $dao->inserir($model);

        header('Location: /dashboard/veterinario/listar');
        die();
    }

    public function editar($params)
    {
        $id = $params['id'] ?? ($params[0] ?? null);

        $dao = new VeterinarioDAO();
        $veterinario = $dao->buscarPorId($id);

        $this->getView()->title = 'Editar Veterinário';
        $this->getView()->title_pagina = 'Editar Veterinário';
        $this->getView()->veterinario = $veterinario;

        $this->render('../dashboard/veterinario_editar', 'dashboard');
    }

    public function alterar()
    {
        $model = new VeterinarioModel();

        $model->__set('vet_id', $_POST['vet_id'] ?? null);
        $model->__set('vet_nome', $_POST['vet_nome'] ?? '');
        $model->__set('vet_cpf', $_POST['vet_cpf'] ?? '');
        $model->__set('vet_crmv', $_POST['vet_crmv'] ?? '');
        $model->__set('vet_dn', $_POST['vet_dn'] ?? null);

        $model->__set('vet_cep', $_POST['vet_cep'] ?? '');
        $model->__set('vet_estado', $_POST['vet_estado'] ?? '');
        $model->__set('vet_cidade', $_POST['vet_cidade'] ?? '');
        $model->__set('vet_bairro', $_POST['vet_bairro'] ?? '');
        $model->__set('vet_logradouro', $_POST['vet_logradouro'] ?? '');
        $model->__set('vet_numero', $_POST['vet_numero'] ?? '');
        $model->__set('vet_complemento', $_POST['vet_complemento'] ?? '');

        $model->__set('vet_tel1', $_POST['vet_tel1'] ?? '');
        $model->__set('vet_tel2', $_POST['vet_tel2'] ?? '');

        $dao = new VeterinarioDAO();
        $dao->alterar($model);

        header('Location: /dashboard/veterinario/listar');
        die();
    }

    public function excluir()
    {
        $id = $_POST['vet_id'] ?? null;

        $dao = new VeterinarioDAO();
        $dao->excluir($id);

        header('Location: /dashboard/veterinario/listar');
        die();
    }

    public function validaAutenticacao()
    {
        // implementar depois se necessário
    }
}