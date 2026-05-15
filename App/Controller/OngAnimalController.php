<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\OngAnimalDAO;
use App\Model\OngAnimalModel;

class OngAnimalController extends Action {

    public function listar() {

        $dao = new OngAnimalDAO();

        $this->getView()->ongAnimais = $dao->listar();

        $this->getView()->title = 'ONG x Animal';
        $this->getView()->title_pagina = 'Relacionamento ONG x Animal';

        $this->render('../dashboard/onganimal_listar', 'dashboard');
    }

    public function cadastro() {

        $this->getView()->title = 'Cadastro ONG x Animal';
        $this->getView()->title_pagina = 'Cadastro ONG x Animal';

        $this->render('../dashboard/onganimal_cadastro', 'dashboard');
    }

    public function cadastrar() {

        $ongAnimal = new OngAnimalModel();

        $ongAnimal->__set('fk_ong_id', $_POST['fk_ong_id']);
        $ongAnimal->__set('fk_animal_id', $_POST['fk_animal_id']);

        $dao = new OngAnimalDAO();
        $dao->inserir($ongAnimal);

        header('Location: /dashboard/onganimal/listar');
        die();
    }

    public function editar($params) {

        $dao = new OngAnimalDAO();

        $this->getView()->ongAnimal = $dao->buscarPorId($params['id']);

        $this->getView()->title = 'Editar ONG x Animal';
        $this->getView()->title_pagina = 'Editar ONG x Animal';

        $this->render('../dashboard/onganimal_editar', 'dashboard');
    }

    public function alterar() {

        $ongAnimal = new OngAnimalModel();

        $ongAnimal->__set('onganl_id', $_POST['onganl_id']);
        $ongAnimal->__set('fk_ong_id', $_POST['fk_ong_id']);
        $ongAnimal->__set('fk_animal_id', $_POST['fk_animal_id']);

        $dao = new OngAnimalDAO();
        $dao->alterar($ongAnimal);

        header('Location: /dashboard/onganimal/listar');
        die();
    }

    public function excluir() {

        $dao = new OngAnimalDAO();

        $dao->excluir($_POST['id']);

        header('Location: /dashboard/onganimal/listar');
        die();
    }

    public function validaAutenticacao() {

        if (
            !isset($_SESSION['id']) ||
            $_SESSION['id'] == '' ||
            !isset($_SESSION['nome']) ||
            $_SESSION['nome'] == ''
        ) {
            header('Location: /login');
            die();
        }
    }
}