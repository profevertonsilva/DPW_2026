<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\AnimalDAO;
use App\Model\AnimalModel;

class AnimalController extends Action
{
    public function listar()
    {
        $dao     = new AnimalDAO();
        $animais = $dao->listar();

        $this->getView()->title        = 'Animais';
        $this->getView()->title_pagina = 'Listar Animais';
        $this->getView()->animais      = $animais;

        $this->render('../dashboard/animal_listar', 'dashboard');
    }

    public function cadastro()
    {
        $this->getView()->title        = 'Cadastro de Animal';
        $this->getView()->title_pagina = 'Cadastro de Animal';

        $this->render('../dashboard/animal_cadastro', 'dashboard');
    }

    public function cadastrar()
    {
        $model = new AnimalModel();
        $model->__set('nome',            $_POST['nome']            ?? '');
        $model->__set('data_nascimento', $_POST['data_nascimento'] ?? null);
        $model->__set('sexo',            $_POST['sexo']            ?? '');
        $model->__set('fk_especie_id',   $_POST['fk_especie_id']   ?? null);
        $model->__set('cor',             $_POST['cor']             ?? '');
        $model->__set('castrado',        !empty($_POST['castrado']));
        $model->__set('descricao',       $_POST['descricao']       ?? '');
        $model->__set('porte',           $_POST['porte']           ?? '');
        $model->__set('localizacao',     $_POST['localizacao']     ?? '');
        $model->__set('foto',            $_POST['foto']            ?? '');
        $model->__set('status',          $_POST['status']          ?? 'disponivel');

        $dao = new AnimalDAO();
        $dao->inserir($model);

        header('Location: /dashboard/animal/listar');
        die();
    }

    public function editar($params)
    {
        $id  = $params['id'] ?? ($params[0] ?? null);

        $dao    = new AnimalDAO();
        $animal = $dao->buscarPorId($id);

        $this->getView()->title        = 'Editar Animal';
        $this->getView()->title_pagina = 'Editar Animal';
        $this->getView()->animal       = $animal;
        $this->getView()->params       = $params;

        $this->render('../dashboard/animal_editar', 'dashboard');
    }

    public function alterar()
    {
        $model = new AnimalModel();
        $model->__set('id',              $_POST['id']              ?? null);
        $model->__set('nome',            $_POST['nome']            ?? '');
        $model->__set('data_nascimento', $_POST['data_nascimento'] ?? null);
        $model->__set('sexo',            $_POST['sexo']            ?? '');
        $model->__set('fk_especie_id',   $_POST['fk_especie_id']   ?? null);
        $model->__set('cor',             $_POST['cor']             ?? '');
        $model->__set('castrado',        !empty($_POST['castrado']));
        $model->__set('descricao',       $_POST['descricao']       ?? '');
        $model->__set('porte',           $_POST['porte']           ?? '');
        $model->__set('localizacao',     $_POST['localizacao']     ?? '');
        $model->__set('foto',            $_POST['foto']            ?? '');
        $model->__set('status',          $_POST['status']          ?? 'disponivel');

        $dao = new AnimalDAO();
        $dao->alterar($model);

        header('Location: /dashboard/animal/listar');
        die();
    }

    public function excluir()
    {
        $id = $_POST['id'] ?? null;

        $dao = new AnimalDAO();
        $dao->excluir($id);

        header('Location: /dashboard/animal/listar');
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
