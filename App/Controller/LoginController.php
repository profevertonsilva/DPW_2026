<?php

namespace App\Controller;

use FW\Controller\Action;

class LoginController extends Action {

    public function autenticar() {
        // TODO: implementar autenticação do usuário
        header('Location: /dashboard');
        die();
    }

    public function logout() {
        // TODO: implementar encerramento de sessão
        session_start();
        session_destroy();
        header('Location: /');
        die();
    }

    public function validaAutenticacao() {
        // Rota pública, não requer autenticação
    }
}
