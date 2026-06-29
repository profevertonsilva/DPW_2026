<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\RastreadorDAO;
use App\Model\RastreadorModel;

class RastreadorController extends Action
{
    public function listar()
    {
        $dao = new RastreadorDAO();
        $rastreadores = $dao->listar();

        $this->getView()->title       = 'Rastreadores';
        $this->getView()->title_pagina = 'Listar Rastreadores';
        $this->getView()->rastreadores = $rastreadores;

        $this->render('../dashboard/rastreador_listar', 'dashboard');
    }

    public function cadastro()
    {
        $this->getView()->title       = 'Cadastro de Rastreador';
        $this->getView()->title_pagina = 'Cadastro de Rastreador';

        $this->render('../dashboard/rastreador_cadastro', 'dashboard');
    }

    public function cadastrar()
    {
        $model = new RastreadorModel();
        $model->__set('rast_nome',        $_POST['nome']        ?? '');
        $model->__set('rast_cpf',         $_POST['cpf']         ?? '');
        $model->__set('rast_cep',         $_POST['cep']         ?? '');
        $model->__set('rast_estado',      $_POST['estado']      ?? '');
        $model->__set('rast_cidade',      $_POST['cidade']      ?? '');
        $model->__set('rast_bairro',      $_POST['bairro']      ?? '');
        $model->__set('rast_logradouro',  $_POST['logradouro']  ?? '');
        $model->__set('rast_numero',      $_POST['numero']      ?? '');
        $model->__set('rast_complemento', $_POST['complemento'] ?? '');
        $model->__set('rast_tel1',        $_POST['telefone']    ?? '');
        $model->__set('rast_tel2',        $_POST['telefone_2']  ?? '');

        $dao = new RastreadorDAO();
        $dao->inserir($model);

        header('Location: /dashboard/rastreador/listar');
        die();
    }

    public function editar($params)
    {
        $id  = $params['id'] ?? ($params[0] ?? null);
        $dao = new RastreadorDAO();
        $rastreador = $dao->buscarPorId($id);

        $this->getView()->title        = 'Editar Rastreador';
        $this->getView()->title_pagina = 'Editar Rastreador';
        $this->getView()->rastreador   = $rastreador;

        $this->render('../dashboard/rastreador_editar', 'dashboard');
    }

    public function alterar()
    {
        $model = new RastreadorModel();
        $model->__set('rast_id',          $_POST['id']          ?? null);
        $model->__set('rast_nome',        $_POST['nome']        ?? '');
        $model->__set('rast_cpf',         $_POST['cpf']         ?? '');
        $model->__set('rast_cep',         $_POST['cep']         ?? '');
        $model->__set('rast_estado',      $_POST['estado']      ?? '');
        $model->__set('rast_cidade',      $_POST['cidade']      ?? '');
        $model->__set('rast_bairro',      $_POST['bairro']      ?? '');
        $model->__set('rast_logradouro',  $_POST['logradouro']  ?? '');
        $model->__set('rast_numero',      $_POST['numero']      ?? '');
        $model->__set('rast_complemento', $_POST['complemento'] ?? '');
        $model->__set('rast_tel1',        $_POST['telefone']    ?? '');
        $model->__set('rast_tel2',        $_POST['telefone_2']  ?? '');

        $dao = new RastreadorDAO();
        $dao->alterar($model);

        header('Location: /dashboard/rastreador/listar');
        die();
    }

    public function excluir()
    {
        $id  = $_POST['id'] ?? null;
        $dao = new RastreadorDAO();
        $dao->excluir($id);

        header('Location: /dashboard/rastreador/listar');
        die();
    }

    public function validaAutenticacao()
    {
    }
}
