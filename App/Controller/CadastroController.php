<?php

namespace App\Controller;

use FW\Controller\Action;
use App\Model\AdotanteModel;
use App\Model\LoginModel;
use App\DAO\AdotanteDAO;
use App\DAO\LoginDAO;
use App\Exception\CpfJaCadastradoException;
use App\Exception\EmailJaCadastradoException;
use App\Service\CadastroService;
use FW\Controller\FuncoesGlobais;

class CadastroController extends Action
{

    public function cadastro()
    {
        $this->getView()->title       = 'Cadastro de Usuários';
        $this->getView()->title_pagina = 'Criação de Login';

        $this->render('../dashboard/cadastro', 'dashboard');
    }

    public function cadastrar()
    {
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

        if (empty($nome) || empty($cpf) || empty($email)) {
            header("Location: /cadastro?erro=nome-cpf-email-vazio");
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
            header('Location: /cadastro?erro=data-nascimento-vazia');
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

        $adotanteDao = new AdotanteDAO();
        $verificacaoSeExisteCpf = $adotanteDao->buscarPorCPF($cpf);

        if ($verificacaoSeExisteCpf) {
            header('Location: /cadastro?erro=8');
            die();
        }

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

        try {
            CadastroService::cadastrarAdotante($loginModel, $adotanteModel);
        } catch(EmailJaCadastradoException $ex) {
            header('Location: /cadastro?erro=email-cadastrado');
            die();
        } catch(CpfJaCadastradoException $ex) {
            header('Location: /cadastro?erro=cpf-cadastrado');
            die();
        } catch(\Throwable $ex) {
            header('Location: /cadastro?erro=inesperado');
            die();
        }

        header('Location: /');
        die();
    }

    public function validaAutenticacao() {}
}
