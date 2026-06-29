<?php

namespace App\Controller;

use FW\Controller\Action;
use FW\DB\Connection;

class ResgatesController extends Action
{
    protected $resgates = [];

    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->validaAutenticacao();

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

        // Only campo team (rastreador) can access this page
        if ($role !== 'campo') {
            header('Location: /dashboard');
            die();
        }

        // Load resgates from database
        $resgates = [];
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            $sql = "SELECT r.*, c.assunto as chamado_assunto, c.localizacao as chamado_localizacao, 
                    c.urgencia as chamado_urgencia, c.contato_nome, c.contato_telefone
                    FROM resgate r 
                    LEFT JOIN chamado c ON r.fk_chamado_id = c.id 
                    ORDER BY r.data_resgate DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $resgates = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Log error but continue with empty array
            error_log("Error loading resgates: " . $e->getMessage());
        }

        // Pass resgates to the view using protected property
        $this->resgates = $resgates;
        $this->render('includes/contents/resgates_content', 'dashboard');
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
    }
}
