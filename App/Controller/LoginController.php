<?php

namespace App\Controller;

use App\DAO\LoginDAO;
use App\Model\LoginModel;
use FW\Controller\Action;

class LoginController extends Action
{

    public function autenticar()
    {

        if (!isset($_POST['email']) || !isset($_POST['senha'])) {
            header('Location: /?erro=1');
            die();
        }

        $email = trim($_POST['email']);
        $senha = $_POST['senha'];

        $loginDAO = new LoginDAO();
        $login = $loginDAO->buscarPorEmail($email);

        if (!$login) {
            header('Location: /?erro=3');
            die();
        }

        $senhaSalva = $login->__get('senha');

        if (!password_verify($senha, $senhaSalva)) {
            header('Location: /?erro=1');
            die();
        }

        if ($login->__get('status') !== 'a') {
            header('Location: /?erro=2');
            die();
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION['id'] = $login->__get('id');
        $_SESSION['email'] = $login->__get('email');
        $_SESSION['tipo_usuario'] = $login->__get('tipo_usuario');
        $_SESSION['status'] = $login->__get('status');

        header('Location: /dashboard');
        die();
    }

    public function alterarSenha()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
            header('Location: /login?erro=2');
            die();
        }

        $senhaAtual = $_POST["senha_atual"] ?? '';
        $senhaNova = $_POST["senha_nova"] ?? '';
        $senhaConfirmacao = $_POST["senha_confirmacao"] ?? '';

        if (empty($senhaAtual) || empty($senhaNova) || empty($senhaConfirmacao)) {
            header('Location:/perfil/alterarSenha?erro=1');
            die();
        }

        if (!($senhaNova !== $senhaConfirmacao)) {
            header('Location: /perfil/alterarSenha?erro=2');
            die();
        }

        if (strlen($senhaNova) < 8) {
            header('Location: /perfil/alterarSenha?erro=3');
            die();
        }

        $loginDAO = new LoginDAO();
        $login = $loginDAO->buscarPorId($_SESSION['id']);

        if (!$login) {
            header('Location:/perfil/alterarSenha?erro=4');
            die();
        }

        $senhaSalva = $login->__get('senha');

        if (!password_verify($senhaAtual, $senhaSalva)) {
            // TODO: criar view /perfil/alterarSenha
            header('Location: /perfil/alterarSenha?erro=5');
            die();
        }



        $loginModel = new LoginModel();
        $loginModel->__set('id', $_SESSION['id']);
        $loginModel->__set('senha', $senhaNova);

        $loginDAO->alterarSenha($loginModel);

        header('Location: /perfil?senha=alterada');
        die();
    }

    public function logout()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        session_destroy();
        header('Location: /');
        die();
    }

    public function validaAutenticacao()
    {
        // Rota pública, não requer autenticação
    }
}
