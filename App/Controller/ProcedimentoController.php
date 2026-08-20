<?php
/*
 * Controller para RF#16 - Sistema de Gerenciamento de Procedimentos Médicos
 *
 * Tabela: procedimento (append-only — RNF#08)
 */

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\ProcedimentoDAO;
use App\DAO\AnimalDAO;
use App\Model\ProcedimentoModel;
use App\Logger\Logger;

class ProcedimentoController extends Action
{
    public function listar()
    {
        $dao = new ProcedimentoDAO();
        $procedimentos = $dao->listar();

        $this->getView()->title = "Procedimentos Médicos";
        $this->getView()->title_pagina = "Listar Procedimentos";
        $this->getView()->procedimentos = $procedimentos;

        $this->render("../dashboard/procedimento_listar", "dashboard");
    }

    public function cadastro()
    {
        $animalDAO = new AnimalDAO();

        $this->getView()->title = "Registrar Procedimento";
        $this->getView()->title_pagina = "Registrar Procedimento";
        $this->getView()->animais = $animalDAO->listar();

        $this->render("../dashboard/procedimento_cadastro", "dashboard");
    }

    public function cadastrar()
    {
        $logger = new Logger();

        $model = new ProcedimentoModel();
        $model->__set("nome", $_POST["nome"] ?? "");
        $model->__set("tipo", $_POST["tipo"] ?? "");
        $model->__set("data", $_POST["data"] ?? null);
        $model->__set("veterinario_nome", $_POST["veterinario_nome"] ?? null);
        $model->__set("observacoes", $_POST["observacoes"] ?? null);
        $model->__set("anexo_url", $_POST["anexo_url"] ?? null);
        $model->__set("fk_animal_id", $_POST["fk_animal_id"] ?? null);
        $model->__set("fk_login_id", $_SESSION["id"] ?? null);
        $model->__set("criado_por", $_SESSION["nome"] ?? "Sistema");

        $dao = new ProcedimentoDAO();
        $procedimentoId = $dao->inserir($model);

        $logger->info("Procedimento registrado com sucesso", [
            "procedimento_id" => $procedimentoId,
        ]);

        header("Location: /dashboard/procedimento/listar");
        die();
    }

    public function editar($params)
    {
        $id = $params["id"] ?? ($params[0] ?? null);

        $procedimentoDAO = new ProcedimentoDAO();
        $animalDAO = new AnimalDAO();
        $procedimento = $procedimentoDAO->buscarPorId($id);

        $this->getView()->title = "Editar Procedimento";
        $this->getView()->title_pagina = "Editar Procedimento";
        $this->getView()->procedimento = $procedimento;
        $this->getView()->animais = $animalDAO->listar();
        $this->getView()->params = $params;

        $this->render("../dashboard/procedimento_editar", "dashboard");
    }

    public function alterar()
    {
        $logger = new Logger();

        $model = new ProcedimentoModel();
        $model->__set("id", $_POST["id"] ?? null);
        $model->__set("nome", $_POST["nome"] ?? "");
        $model->__set("tipo", $_POST["tipo"] ?? "");
        $model->__set("data", $_POST["data"] ?? null);
        $model->__set("veterinario_nome", $_POST["veterinario_nome"] ?? null);
        $model->__set("observacoes", $_POST["observacoes"] ?? null);
        $model->__set("anexo_url", $_POST["anexo_url"] ?? null);
        $model->__set("fk_animal_id", $_POST["fk_animal_id"] ?? null);

        $dao = new ProcedimentoDAO();
        $dao->alterar($model);

        $logger->info("Procedimento alterado com sucesso", [
            "procedimento_id" => $_POST["id"] ?? null,
        ]);

        header("Location: /dashboard/procedimento/listar");
        die();
    }

    /**
     * Exclusão bloqueada por política append-only (RNF#08).
     * Redireciona para listagem.
     */
    public function excluir()
    {
        $logger = new Logger();
        $logger->warning(
            "Tentativa de exclusão de procedimento bloqueada (RNF#08)",
            [
                "procedimento_id" => $_POST["id"] ?? null,
                "usuario" => $_SESSION["nome"] ?? "desconhecido",
            ],
        );

        header("Location: /dashboard/procedimento/listar");
        die();
    }

    public function validaAutenticacao()
    {
        if (
            !isset($_SESSION["id"]) ||
            $_SESSION["id"] == "" ||
            !isset($_SESSION["nome"]) ||
            $_SESSION["nome"] == ""
        ) {
            header("Location: /login");
            die();
        }
    }
}
