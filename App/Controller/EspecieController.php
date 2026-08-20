<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\EspecieDAO;
use App\Model\EspecieModel;
use App\Validador\ValidadorEspecie;
use App\Logger\Logger;

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
        $validador = new ValidadorEspecie();
        $logger = new Logger();
        
        if (!$validador->validarFormulario($_POST)) {
            $logger->warning('Validação falhou ao cadastrar espécie', [
                'erros' => $validador->obterErros()
            ]);
            
            $this->getView()->title        = 'Cadastro de Espécie';
            $this->getView()->title_pagina = 'Cadastro de Espécie';
            $this->getView()->erros        = $validador->obterErros();
            $this->getView()->dados        = $_POST;
            
            $this->render('../dashboard/especie_cadastro', 'dashboard');
            return;
        }

        $model = new EspecieModel();
        $model->__set('nome', $_POST['nome'] ?? '');

        $dao = new EspecieDAO();
        $especieId = $dao->inserir($model);
        
        $logger->info('Espécie cadastrada com sucesso', ['especie_id' => $especieId]);

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
        $validador = new ValidadorEspecie();
        $logger = new Logger();
        
        if (!$validador->validarFormulario($_POST)) {
            $logger->warning('Validação falhou ao alterar espécie', [
                'erros' => $validador->obterErros(),
                'especie_id' => $_POST['id'] ?? null
            ]);
            
            $id  = $_POST['id'] ?? null;
            $dao    = new EspecieDAO();
            $especie = $dao->buscarPorId($id);

            $this->getView()->title        = 'Editar Espécie';
            $this->getView()->title_pagina = 'Editar Espécie';
            $this->getView()->especie      = $especie;
            $this->getView()->erros        = $validador->obterErros();
            
            $this->render('../dashboard/especie_editar', 'dashboard');
            return;
        }

        $model = new EspecieModel();
        $model->__set('id',   $_POST['id']   ?? null);
        $model->__set('nome', $_POST['nome'] ?? '');

        $dao = new EspecieDAO();
        $dao->alterar($model);
        
        $logger->info('Espécie alterada com sucesso', ['especie_id' => $_POST['id'] ?? null]);

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