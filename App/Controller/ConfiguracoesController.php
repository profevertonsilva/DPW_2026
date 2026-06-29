<?php

namespace App\Controller;

use FW\Controller\Action;

class ConfiguracoesController extends Action
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $tipoUsuario = $_SESSION['tipo_usuario'] ?? 'adotante';

        // Mapeia o tipo_usuario do banco para os roles do sistema
        $roleMap = [
            'administrador' => 'admin',
            'ong' => 'ong',
            'veterinario' => 'vet',
            'moderador' => 'campo',
            'adotante' => 'usuario'
        ];

        $role = $roleMap[$tipoUsuario] ?? 'usuario';

        if ($role !== 'admin') {
            header('Location: /dashboard');
            die();
        }

        $this->render('includes/contents/configuracoes_content', 'dashboard');
    }

    public function validaAutenticacao()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
            header('Location: /login?erro=2');
            die();
        }

        $tipoUsuario = $_SESSION['tipo_usuario'] ?? 'adotante';

        // Mapeia o tipo_usuario do banco para os roles do sistema
        $roleMap = [
            'administrador' => 'admin',
            'ong' => 'ong',
            'veterinario' => 'vet',
            'moderador' => 'campo',
            'adotante' => 'usuario'
        ];

        $role = $roleMap[$tipoUsuario] ?? 'usuario';

        if ($role !== 'admin') {
            header('Location: /dashboard');
            die();
        }
    }
}
