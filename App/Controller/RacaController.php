<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\RacaDAO;
use App\DAO\EspecieDAO;
use App\Model\RacaModel;
use App\Validador\ValidadorRaca;
use App\Logger\Logger;

class RacaController extends Action
{
    public function listar()
    {
        $dao   = new RacaDAO();
        $racas = $dao->listar();

        $this->getView()->title        = 'Raças';
        $this->getView()->title_pagina = 'Listar Raças';
        $this->getView()->racas        = $racas;

        $this->render('../dashboard/raca_listar', 'dashboard');
    }

    public function cadastro()
    {
        $especieDAO = new EspecieDAO();

        $this->getView()->title        = 'Cadastro de Raça';
        $this->getView()->title_pagina = 'Cadastro de Raça';
        $this->getView()->especies     = $especieDAO->listar();

        $this->render('../dashboard/raca_cadastro', 'dashboard');
    }

    public function cadastrar()
    {
        $validador = new ValidadorRaca();
        $logger = new Logger();
        
        if (!$validador->validarFormulario($_POST)) {
            $logger->warning('Validação falhou ao cadastrar raça', [
                'erros' => $validador->obterErros()
            ]);
            
            $especieDAO = new EspecieDAO();
            $this->getView()->title        = 'Cadastro de Raça';
            $this->getView()->title_pagina = 'Cadastro de Raça';
            $this->getView()->especies     = $especieDAO->listar();
            $this->getView()->erros        = $validador->obterErros();
            $this->getView()->dados        = $_POST;
            
            $this->render('../dashboard/raca_cadastro', 'dashboard');
            return;
        }

        $model = new RacaModel();
        $model->__set('nome',          $_POST['nome']          ?? '');
        $model->__set('fk_especie_id', $_POST['fk_especie_id'] ?? null);

        $dao = new RacaDAO();
        $racaId = $dao->inserir($model);
        
        $logger->info('Raça cadastrada com sucesso', ['raca_id' => $racaId]);

        header('Location: /dashboard/raca/listar');
        die();
    }

    public function editar($params)
    {
        $id = $params['id'] ?? ($params[0] ?? null);

        $racaDAO    = new RacaDAO();
        $especieDAO = new EspecieDAO();
        $raca       = $racaDAO->buscarPorId($id);

        $this->getView()->title        = 'Editar Raça';
        $this->getView()->title_pagina = 'Editar Raça';
        $this->getView()->raca         = $raca;
        $this->getView()->especies     = $especieDAO->listar();
        $this->getView()->params       = $params;

        $this->render('../dashboard/raca_editar', 'dashboard');
    }

    public function alterar()
    {
        $validador = new ValidadorRaca();
        $logger = new Logger();
        
        if (!$validador->validarFormulario($_POST)) {
            $logger->warning('Validação falhou ao alterar raça', [
                'erros' => $validador->obterErros(),
                'raca_id' => $_POST['id'] ?? null
            ]);
            
            $id = $_POST['id'] ?? null;
            $racaDAO    = new RacaDAO();
            $especieDAO = new EspecieDAO();
            $raca       = $racaDAO->buscarPorId($id);

            $this->getView()->title        = 'Editar Raça';
            $this->getView()->title_pagina = 'Editar Raça';
            $this->getView()->raca         = $raca;
            $this->getView()->especies     = $especieDAO->listar();
            $this->getView()->erros        = $validador->obterErros();
            
            $this->render('../dashboard/raca_editar', 'dashboard');
            return;
        }

        $model = new RacaModel();
        $model->__set('id',            $_POST['id']            ?? null);
        $model->__set('nome',          $_POST['nome']          ?? '');
        $model->__set('fk_especie_id', $_POST['fk_especie_id'] ?? null);

        $dao = new RacaDAO();
        $dao->alterar($model);
        
        $logger->info('Raça alterada com sucesso', ['raca_id' => $_POST['id'] ?? null]);

        header('Location: /dashboard/raca/listar');
        die();
    }

    public function excluir()
    {
        $id = $_POST['id'] ?? null;

        $dao = new RacaDAO();
        $dao->excluir($id);

        header('Location: /dashboard/raca/listar');
        die();
    }

    public function porEspecie()
    {
        header('Content-Type: application/json');

        $fk_especie_id = $_GET['fk_especie_id'] ?? null;

        if (empty($fk_especie_id)) {
            echo json_encode([]);
            die();
        }

        $dao = new RacaDAO();
        $racas = $dao->listarPorEspecie((int) $fk_especie_id);

        $resultado = [];
        foreach ($racas as $raca) {
            $resultado[] = [
                'id'   => (int) $raca->__get('id'),
                'nome' => htmlspecialchars($raca->__get('nome')),
            ];
        }

        echo json_encode($resultado);
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
