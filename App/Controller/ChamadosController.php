<?php

namespace App\Controller;

use FW\Controller\Action;
use FW\DB\Connection;

class ChamadosController extends Action
{
    protected $chamados = [];

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

        // Load chamados from database
        $chamados = [];
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            $sql = "SELECT c.*, o.nome as ong_nome 
                    FROM chamado c 
                    LEFT JOIN ong o ON c.fk_ong_id = o.id 
                    ORDER BY c.data_criacao DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $chamados = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Log error but continue with empty array
            error_log("Error loading chamados: " . $e->getMessage());
        }

        // Pass chamados to the view using protected property
        $this->chamados = $chamados;
        $this->render('includes/contents/chamados_content', 'dashboard');
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

        // Only campo team (rastreador) can access this page
        if ($role !== 'campo') {
            header('Location: /dashboard');
            die();
        }
    }

    public function criar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Não autenticado']);
            return;
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

        // Only admin and ong can create chamados
        if (!in_array($role, ['admin', 'ong'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Sem permissão para criar chamados']);
            return;
        }

        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            return;
        }

        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            // Get ONG ID based on user role
            $fk_ong_id = null;
            if ($role === 'ong') {
                $stmt = $conn->prepare("SELECT id FROM ong WHERE fk_login_id = ?");
                $stmt->execute([$_SESSION['id']]);
                $ong = $stmt->fetch();
                $fk_ong_id = $ong['id'] ?? null;
            } elseif ($role === 'admin') {
                // For admin, use the first ONG or a default
                $stmt = $conn->prepare("SELECT id FROM ong LIMIT 1");
                $stmt->execute();
                $ong = $stmt->fetch();
                $fk_ong_id = $ong['id'] ?? null;
            }

            // If no ONG found, allow creating chamado without ONG association
            // This is useful for admins or when ONG doesn't exist yet

            // Insert chamado
            $sql = "INSERT INTO chamado (fk_ong_id, fk_denuncia_id, tipo, urgencia, assunto, localizacao, descricao, status, origem, contato_nome, contato_telefone, data_criacao, data_atualizacao) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([
                $fk_ong_id,
                $input['fk_denuncia_id'] ?? null,
                $input['tipo'],
                $input['urgencia'],
                $input['assunto'],
                $input['localizacao'],
                $input['descricao'],
                'pendente',
                $input['origem'] ?? 'manual',
                $input['contato_nome'] ?? null,
                $input['contato_telefone'] ?? null
            ]);

            if ($result) {
                $chamadoId = $conn->lastInsertId();
                echo json_encode(['success' => true, 'message' => 'Chamado criado com sucesso', 'chamado_id' => $chamadoId]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao criar chamado']);
            }
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
        }
    }
}
