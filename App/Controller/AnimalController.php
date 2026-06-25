<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\AnimalDAO;
use App\Model\AnimalModel;

class AnimalController extends Action {

    public function listar() {
        // Busca os animais salvos no banco para mandar para a tabela
        $dao = new AnimalDAO();
        $animais = $dao->listar();

        $this->getView()->title = 'Animais';
        $this->getView()->title_pagina = 'Listar Animais';
        $this->getView()->animais = $animais; // Disponibiliza a lista na View

        $this->render('../dashboard/animal_listar', 'dashboard');
    }

    public function cadastro() {
        // Se houver envio de dados via POST, o Controller intercepta e salva imediatamente
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || !empty($_POST)) {
            try {
                $model = new AnimalModel();
                
                $model->__set('nome',            $_POST['nome']            ?? '');
                $model->__set('data_nascimento', $_POST['data_nascimento'] ?? null);
                $model->__set('sexo',            $_POST['sexo']            ?? '');
                $model->__set('cor',             $_POST['cor']             ?? '');
                $model->__set('porte',           $_POST['porte']           ?? '');
                $model->__set('status',          $_POST['status']          ?? 'regular');
                $model->__set('castrado',        $_POST['castrado']        ?? 'Não');
                $model->__set('foto',            $_POST['foto']            ?? '');
                $model->__set('descricao',       $_POST['descricao']       ?? '');

                $dao = new AnimalDAO();
                $dao->inserir($model);

                // Desvio seguro: cospe o redirecionamento em JS antes que a Navbar quebre o fluxo
                echo "<script>window.location.href = '/dashboard/animal/listar';</script>";
                exit();
            } catch (\Exception $e) {
                echo "<h3>Erro ao salvar animal:</h3><pre>" . $e->getMessage() . "</pre>";
                die();
            }
        }

        // Se for acesso normal (GET), renderiza o formulário padrão de cadastro
        $this->getView()->title = 'Cadastro de Animal';
        $this->getView()->title_pagina = 'Cadastro de Animal';

        $this->render('../dashboard/animal_cadastro', 'dashboard');
    }

    public function cadastrar() {
        // Garante suporte caso as rotas chamem o método no infinitivo
        $this->cadastro();
    }

    public function editar($params) {
        // SOLUÇÃO DO ERRO: Tenta pegar o ID do $_GET primeiro (?id=1) antes de olhar o $params
        $id = $_GET['id'] ?? $params['id'] ?? ($params[0] ?? null);
        
        $dao = new AnimalDAO();
        $animal = $dao->buscarPorId($id);

        $this->getView()->title = 'Editar Animal';
        $this->getView()->title_pagina = 'Editar Animal';
        $this->getView()->animal = $animal; // Passa o objeto do animal para preencher os inputs

        $this->render('../dashboard/animal_editar', 'dashboard');
    }

    public function alterar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $model = new AnimalModel();
                
                $model->__set('id',              $_POST['id']              ?? null);
                $model->__set('nome',            $_POST['nome']            ?? '');
                $model->__set('data_nascimento', $_POST['data_nascimento'] ?? null);
                $model->__set('sexo',            $_POST['sexo']            ?? '');
                $model->__set('cor',             $_POST['cor']             ?? '');
                $model->__set('porte',           $_POST['porte']           ?? '');
                $model->__set('status',          $_POST['status']          ?? 'regular');
                $model->__set('castrado',        $_POST['castrado']        ?? 'Não');
                $model->__set('foto',            $_POST['foto']            ?? '');
                $model->__set('descricao',       $_POST['descricao']       ?? '');

                $dao = new AnimalDAO();
                $dao->alterar($model);

                // Desvio seguro com JS para evitar quebra de cabeçalhos no salvamento
                echo "<script>window.location.href = '/dashboard/animal/listar';</script>";
                exit();
            } catch (\Exception $e) {
                echo "<h3>Erro ao alterar animal:</h3><pre>" . $e->getMessage() . "</pre>";
                die();
            }
        }
    }

    public function excluir() {
        // Pega o ID vindo via POST ou GET do botão de lixeira da listagem
        $id = $_POST['id'] ?? ($_GET['id'] ?? null);
        
        if ($id) {
            try {
                $dao = new AnimalDAO();
                $dao->excluir($id);
            } catch (\Exception $e) {
                echo "<h3>Erro ao excluir animal:</h3><pre>" . $e->getMessage() . "</pre>";
                die();
            }
        }
        
        header('Location: /dashboard/animal/listar');
        die();
    }

    public function validaAutenticacao() {
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /login');
            die();
        }
    }
}