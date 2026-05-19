<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\EspecieDAO;
use App\Model\EspecieModel;

class EspecieController extends Action
{
    public function listar()
    {
        $dao     = new EspecieDAO();
        $especies = $dao->listar();

        $this->getView()->title        = 'Espécies';
        $this->getView()->title_pagina = 'Listar Espécies';
        $this->getView()->especies     = $especies;

        $this->render('../dashboard/especie_listar', 'dashboard');
    }

    public function cadastro()
    {
        $this->getView()->title        = 'Cadastro de Espécie';
        $this->getView()->title_pagina = 'Cadastro de Espécie';

        $this->render('../dashboard/especie_cadastro', 'dashboard');
    }

    public function cadastrar()
    {
        $model = new EspecieModel();
        $model->__set('nome', $_POST['nome'] ?? '');

        $dao = new EspecieDAO();
        $dao->inserir($model);

        header('Location: /dashboard/especie/listar');
        die();
    }

    public function editar($params)
    {
        $id  = $params['id'] ?? ($params[0] ?? null);

        $dao    = new EspecieDAO();
        $especie = $dao->buscarPorId($id);

        $this->getView()->title        = 'Editar Espécie';
        $this->getView()->title_pagina = 'Editar Espécie';
        $this->getView()->especie      = $especie;
        $this->getView()->params       = $params;

        $this->render('../dashboard/especie_editar', 'dashboard');
    }

    public function alterar()
    {
        $model = new EspecieModel();
        $model->__set('id',   $_POST['id']   ?? null);
        $model->__set('nome', $_POST['nome'] ?? '');

        $dao = new EspecieDAO();
        $dao->alterar($model);

        header('Location: /dashboard/especie/listar');
        die();
    }

    public function excluir()
    {
        $id = $_POST['id'] ?? null;

        $dao = new EspecieDAO();
        $dao->excluir($id);

        header('Location: /dashboard/especie/listar');
        die();
    }

    public function validaAutenticacao()
    {
        if (!isset($_SESSION['id'])   || $_SESSION['id']   == '' ||
            !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /login');
            die();
        }
    }
}