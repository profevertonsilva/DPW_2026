<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\AdotanteDAO;
use App\DAO\LoginDAO;
use App\Exception\CpfJaCadastradoException;
use App\Exception\EmailJaCadastradoException;
use App\Middleware\PermissaoMiddleware;
use App\Model\AdotanteModel;
use App\Model\LoginModel;
use App\Service\CadastroService;
use FW\Controller\FuncoesGlobais;

class AdotanteController extends Action
{
    public function listar()
    {
        $this->validaAutenticacao();
        
        $dao = new AdotanteDAO();
        $adotantes = $dao->listar();

        $this->getView()->title       = 'Adotantes';
        $this->getView()->title_pagina = 'Listar Adotantes';
        $this->getView()->adotantes   = $adotantes;

        $this->render('../dashboard/adotante_listar', 'dashboard');
    }

    public function cadastro()
    {
        $this->validaAutenticacao();
        $this->getView()->title       = 'Cadastro de Adotante';
        $this->getView()->title_pagina = 'Cadastro de Adotante';
        $this->render('../dashboard/adotante_cadastro', 'dashboard');
    }

    public function cadastrar()
    {
        $this->validaAutenticacao();
        $global = new FuncoesGlobais();

        $nome = trim($_POST['nome'] ?? '');
        $nome = preg_replace('/\s+/', ' ', $nome);

        $cpf = trim($_POST['cpf'] ?? '');
        $data_nascimento = trim($_POST['data_nascimento'] ?? '');
        $cep = trim($_POST['cep'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';
        $senha_confirmacao = $_POST['senha_confirmacao'] ?? '';
        $estado = trim($_POST['estado'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');
        $bairro = trim($_POST['bairro'] ?? '');
        $logradouro = trim($_POST['logradouro'] ?? '');
        $numero = trim($_POST['numero'] ?? '');
        $complemento = trim($_POST['complemento'] ?? '');
        $telefone_1 = trim($_POST['telefone_1'] ?? '');
        $telefone_2 = trim($_POST['telefone_2'] ?? '');

        $telefone_limpo_1 = $global->limparTelefone($telefone_1);
        $telefone_limpo_2 = $global->limparTelefone($telefone_2);
        $status = trim($_POST['status'] ?? 'bom');
        
        if (empty($nome) || empty($cpf) || empty($email)) {
            header("Location: /cadastro?erro=cpf-nome-email-vazio");
            die();
        }

        if (empty($senha) || empty($senha_confirmacao)) {
            header("Location: /cadastro?erro=senha-vazia");
            die();
        }

        if ($senha !== $senha_confirmacao) {
            header('Location: /cadastro?erro=senhas-diferentes');
            die();
        }

    
        if (empty($data_nascimento)) {
            header('Location: /cadastro?erro=data-vazia');
            die();
        }

        if (empty($telefone_limpo_1)) {
            header('Location: /cadastro?erro=telefone-vazio');
            die();
        }

        if ($numero === '') {
            $numero = null;
        }

        $cpfValido = $global->cpfValido($cpf);
        if (!$cpfValido) {
            header('Location: /cadastro?erro=cpf-invalido');
            die();
        }

        $cpfLimpo = $global->limparCpf($cpf);

        $loginModel = new LoginModel();
        $loginModel->__set('email', $email);
        $loginModel->__set('senha', $senha);

        $adotanteModel = new AdotanteModel();
        $adotanteModel->__set('nome',   $nome);
        $adotanteModel->__set('cpf',    $cpfLimpo);
        $adotanteModel->__set('data_nascimento', $data_nascimento);
        $adotanteModel->__set('cep',    $cep);
        $adotanteModel->__set('estado', $estado);
        $adotanteModel->__set('cidade', $cidade);
        $adotanteModel->__set('bairro', $bairro);
        $adotanteModel->__set('logradouro',  $logradouro);
        $adotanteModel->__set('numero', $numero);
        $adotanteModel->__set('complemento', $complemento);
        $adotanteModel->__set('telefone_1', $telefone_limpo_1);
        $adotanteModel->__set('telefone_2', $telefone_limpo_2);
        $adotanteModel->__set('status', $status);
        
        try {
            CadastroService::cadastrarAdotante($loginModel, $adotanteModel);
        } catch(EmailJaCadastradoException $ex) {
            header('Location: /dashboard/adotante/cadastro?erro=email-cadastrado');
            die();
        } catch(CpfJaCadastradoException $ex) {
            header('Location: /dashboard/adotante/cadastro?erro=cpf-cadastrado');
            die();
        }catch(\PDOException $ex) {
            header('Location: /dashboard/adotante/cadastro?erro=cadastro');
            die();
        } catch(\Throwable $ex) {
            header('Location: /dashboard/adotante/cadastro?erro=inesperado');
            die();
        }

        header('Location: /dashboard/adotante/listar');
        die();
    }

    public function editar($params)
    {
        $this->validaAutenticacao();
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
        $this->validaAutenticacao();
        $model = new AdotanteModel();
        $model->__set('id',   $_POST['id']             ?? null);
        $model->__set('nome', $_POST['nome']            ?? '');
        $model->__set('cpf',  $_POST['cpf']             ?? '');
        $model->__set('data_nascimento',   $_POST['data_nascimento'] ?? null);
        $model->__set('cep',  $_POST['cep']             ?? '');
        $model->__set('estado', $_POST['estado']        ?? '');
        $model->__set('cidade', $_POST['cidade']        ?? '');
        $model->__set('bairro', $_POST['bairro']        ?? '');
        $model->__set('logradouro',  $_POST['logradouro']  ?? '');
        $model->__set('numero',      $_POST['numero']      ?? '');
        $model->__set('complemento', $_POST['complemento'] ?? '');
        $model->__set('telefone_1',   $_POST['telefone_1'] ?? '');
        $model->__set('telefone_2',   $_POST['telefone_2'] ?? '');
        $model->__set('status', $_POST['status']     ?? 'bom');

        $dao = new AdotanteDAO();
        $dao->alterar($model);

        header('Location: /dashboard/adotante/listar');
        die();
    }

    public function excluir()
    {
        PermissaoMiddleware::exigirNivel(PermissaoMiddleware::ADMIN);
        $id  = $_POST['id'] ?? null;
        $dao = new AdotanteDAO();
        $dao->excluir($id);

        header('Location: /dashboard/adotante/listar');
        die();
    }

    public function validaAutenticacao()
    {
        PermissaoMiddleware::exigirNivel(PermissaoMiddleware::MODERADOR);
    }
}
