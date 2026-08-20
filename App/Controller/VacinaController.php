<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\VacinaDAO;
use App\DAO\AnimalDAO;
use App\Model\VacinaModel;
use App\Logger\Logger;

class VacinaController extends Action
{
    public function listar()
    {
        $dao     = new VacinaDAO();
        $vacinas = $dao->listar();

        $this->getView()->title        = 'Carteira de Vacinação';
        $this->getView()->title_pagina = 'Listar Vacinas';
        $this->getView()->vacinas      = $vacinas;

        $this->render('../dashboard/vacina_listar', 'dashboard');
    }

    public function cadastro()
    {
        $animalDAO = new AnimalDAO();

        $this->getView()->title        = 'Registrar Vacina';
        $this->getView()->title_pagina = 'Registrar Vacina';
        $this->getView()->animais      = $animalDAO->listar();

        $this->render('../dashboard/vacina_cadastro', 'dashboard');
    }

    public function cadastrar()
    {
        $logger = new Logger();

        $model = new VacinaModel();
        $model->__set('nome',             $_POST['nome']             ?? '');
        $model->__set('data_aplicacao',   $_POST['data_aplicacao']   ?? null);
        $model->__set('data_reforco',     $_POST['data_reforco']     ?? null);
        $model->__set('veterinario_nome', $_POST['veterinario_nome'] ?? null);
        $model->__set('clinica_nome',     $_POST['clinica_nome']     ?? null);
        $model->__set('fk_animal_id',     $_POST['fk_animal_id']     ?? null);
        $model->__set('fk_login_id',      $_SESSION['id']            ?? null);
        $model->__set('criado_por',       $_SESSION['nome']          ?? 'Sistema');

        $dao       = new VacinaDAO();
        $vacinaId  = $dao->inserir($model);

        $logger->info('Vacina registrada com sucesso', ['vacina_id' => $vacinaId]);

        header('Location: /dashboard/vacina/listar');
        die();
    }

    public function editar($params)
    {
        $id = $params['id'] ?? ($params[0] ?? null);

        $vacinaDAO = new VacinaDAO();
        $animalDAO = new AnimalDAO();
        $vacina    = $vacinaDAO->buscarPorId($id);

        $this->getView()->title        = 'Editar Vacina';
        $this->getView()->title_pagina = 'Editar Vacina';
        $this->getView()->vacina       = $vacina;
        $this->getView()->animais      = $animalDAO->listar();
        $this->getView()->params       = $params;

        $this->render('../dashboard/vacina_editar', 'dashboard');
    }

    public function alterar()
    {
        $logger = new Logger();

        $model = new VacinaModel();
        $model->__set('id',               $_POST['id']               ?? null);
        $model->__set('nome',             $_POST['nome']             ?? '');
        $model->__set('data_aplicacao',   $_POST['data_aplicacao']   ?? null);
        $model->__set('data_reforco',     $_POST['data_reforco']     ?? null);
        $model->__set('veterinario_nome', $_POST['veterinario_nome'] ?? null);
        $model->__set('clinica_nome',     $_POST['clinica_nome']     ?? null);
        $model->__set('fk_animal_id',     $_POST['fk_animal_id']     ?? null);

        $dao = new VacinaDAO();
        $dao->alterar($model);

        $logger->info('Vacina alterada com sucesso', ['vacina_id' => $_POST['id'] ?? null]);

        header('Location: /dashboard/vacina/listar');
        die();
    }

    /**
     * Exclusão bloqueada por política append-only (RNF#08).
     * Redireciona para listagem com mensagem de erro.
     */
    public function excluir()
    {
        $logger = new Logger();
        $logger->warning('Tentativa de exclusão de vacina bloqueada (RNF#08)', [
            'vacina_id' => $_POST['id'] ?? null,
            'usuario'   => $_SESSION['nome'] ?? 'desconhecido'
        ]);

        header('Location: /dashboard/vacina/listar');
        die();
    }

    // =========================================================================
    // ALERTAS DE REFORÇO
    // =========================================================================

    /**
     * Exibe o painel de alertas de vacinas com reforço próximo.
     * Permite configurar a janela de alerta via query string (?dias=7, 15, 30).
     */
    public function alertas()
    {
        $dias = (int) ($_GET['dias'] ?? 7);
        if (!in_array($dias, [7, 15, 30], true)) {
            $dias = 7;
        }

        $dao     = new VacinaDAO();
        $vacinas = $dao->buscarReforcosProximos($dias);

        $this->getView()->title        = 'Alertas de Reforço';
        $this->getView()->title_pagina = "Vacinas com reforço em até {$dias} dias";
        $this->getView()->vacinas      = $vacinas;
        $this->getView()->dias         = $dias;

        $this->render('../dashboard/vacina_alertas', 'dashboard');
    }

    /**
     * Retorna contagem de alertas em JSON (para uso em badges/menus).
     */
    public function contarAlertas()
    {
        header('Content-Type: application/json');

        $dias = (int) ($_GET['dias'] ?? 7);
        if (!in_array($dias, [7, 15, 30], true)) {
            $dias = 7;
        }

        $dao     = new VacinaDAO();
        $vacinas = $dao->buscarReforcosProximos($dias);

        echo json_encode(['total' => count($vacinas)]);
        die();
    }

    public function validaAutenticacao()
    {
        if (
            !isset($_SESSION['id'])   || $_SESSION['id']   == '' ||
            !isset($_SESSION['nome']) || $_SESSION['nome'] == ''
        ) {
            header('Location: /login');
            die();
        }
    }
}
