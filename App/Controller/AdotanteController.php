<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\AdotanteDAO;
use App\Model\AdotanteModel;

class AdotanteController extends Action
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
        $this->getView()->title       = 'Cadastro de Adotante';
        $this->getView()->title_pagina = 'Cadastro de Adotante';

        $this->render('../dashboard/adotante_cadastro', 'dashboard');
    }

    public function cadastrar()
    {
        $model = new AdotanteModel();
        $model->__set('adt_nome',   $_POST['nome']            ?? '');
        $model->__set('adt_cpf',    $_POST['cpf']             ?? '');
        $model->__set('adt_dn',     $_POST['data_nascimento'] ?? null);
        $model->__set('adt_cep',    $_POST['cep']             ?? '');
        $model->__set('adt_estado', $_POST['estado']          ?? '');
        $model->__set('adt_cidade', $_POST['cidade']          ?? '');
        $model->__set('adt_bairro', $_POST['bairro']          ?? '');
        $model->__set('adt_logradouro',  $_POST['logradouro']  ?? '');
        $model->__set('adt_numero',      $_POST['numero']      ?? '');
        $model->__set('adt_complemento', $_POST['complemento'] ?? '');
        $model->__set('adt_tel1',   $_POST['telefone_1'] ?? '');
        $model->__set('adt_tel2',   $_POST['telefone_2'] ?? '');
        $model->__set('adt_status', $_POST['status']     ?? 'bom');

        $dao = new AdotanteDAO();
        $dao->inserir($model);

        header('Location: /dashboard/adotante/listar');
        die();
    }

    public function editar($params)
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
        $model->__set('adt_id',   $_POST['id']             ?? null);
        $model->__set('adt_nome', $_POST['nome']            ?? '');
        $model->__set('adt_cpf',  $_POST['cpf']             ?? '');
        $model->__set('adt_dn',   $_POST['data_nascimento'] ?? null);
        $model->__set('adt_cep',  $_POST['cep']             ?? '');
        $model->__set('adt_estado', $_POST['estado']        ?? '');
        $model->__set('adt_cidade', $_POST['cidade']        ?? '');
        $model->__set('adt_bairro', $_POST['bairro']        ?? '');
        $model->__set('adt_logradouro',  $_POST['logradouro']  ?? '');
        $model->__set('adt_numero',      $_POST['numero']      ?? '');
        $model->__set('adt_complemento', $_POST['complemento'] ?? '');
        $model->__set('adt_tel1',   $_POST['telefone_1'] ?? '');
        $model->__set('adt_tel2',   $_POST['telefone_2'] ?? '');
        $model->__set('adt_status', $_POST['status']     ?? 'bom');

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
