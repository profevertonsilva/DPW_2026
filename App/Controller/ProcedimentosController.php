<?php

namespace App\Controller;

use FW\Controller\Action;
use FW\DB\Connection;

class ProcedimentosController extends Action
{
    protected $procedimentos = [];
    protected $animais = [];

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

        // Only veterinario can access this page
        if ($role !== 'vet') {
            header('Location: /dashboard');
            die();
        }

        // Get veterinarian ID from session
        $veterinarioId = $_SESSION['id'] ?? null;

        // Get current month and year from query params or default to current
        $month = $_GET['mes'] ?? date('m');
        $year = $_GET['ano'] ?? date('Y');

        // Load procedimentos from database for the current month
        $procedimentos = [];
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            $sql = "SELECT p.*, a.nome as animal_nome, a.foto as animal_foto 
                    FROM procedimento p 
                    LEFT JOIN animal a ON p.fk_animal_id = a.id 
                    WHERE p.fk_veterinario_id = :veterinario_id 
                    AND YEAR(p.data_hora) = :year 
                    AND MONTH(p.data_hora) = :month
                    ORDER BY p.data_hora ASC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':veterinario_id' => $veterinarioId,
                ':year' => $year,
                ':month' => $month
            ]);
            $procedimentos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error loading procedimentos: " . $e->getMessage());
        }

        // Load animals for selection
        $animais = [];
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            $sql = "SELECT a.id, a.nome, a.foto, e.nome as especie 
                    FROM animal a 
                    LEFT JOIN especie e ON a.fk_especie_id = e.id 
                    ORDER BY a.nome ASC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $animais = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error loading animais: " . $e->getMessage());
        }

        // Pass data to the view
        $this->procedimentos = $procedimentos;
        $this->animais = $animais;
        $this->render('includes/contents/procedimentos_content', 'dashboard');
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

        // Only veterinario can create procedimentos
        if ($role !== 'vet') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Sem permissão para criar procedimentos']);
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

            // Insert procedimento
            $sql = "INSERT INTO procedimento (fk_veterinario_id, fk_animal_id, tipo_procedimento, descricao, data_hora, duracao_minutos, status, observacoes) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([
                $_SESSION['id'],
                $input['fk_animal_id'],
                $input['tipo_procedimento'],
                $input['descricao'] ?? null,
                $input['data_hora'],
                $input['duracao_minutos'] ?? 30,
                'agendado',
                $input['observacoes'] ?? null
            ]);

            if ($result) {
                $procedimentoId = $conn->lastInsertId();
                echo json_encode(['success' => true, 'message' => 'Procedimento agendado com sucesso', 'procedimento_id' => $procedimentoId]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao agendar procedimento']);
            }
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
        }
    }

    public function atualizar()
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

        // Only veterinario can update procedimentos
        if ($role !== 'vet') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Sem permissão para atualizar procedimentos']);
            return;
        }

        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            return;
        }

        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            // Update procedimento
            $sql = "UPDATE procedimento SET 
                    tipo_procedimento = ?, 
                    descricao = ?, 
                    data_hora = ?, 
                    duracao_minutos = ?, 
                    status = ?, 
                    observacoes = ? 
                    WHERE id = ? AND fk_veterinario_id = ?";
            
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([
                $input['tipo_procedimento'],
                $input['descricao'] ?? null,
                $input['data_hora'],
                $input['duracao_minutos'] ?? 30,
                $input['status'] ?? 'agendado',
                $input['observacoes'] ?? null,
                $input['id'],
                $_SESSION['id']
            ]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Procedimento atualizado com sucesso']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar procedimento']);
            }
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
        }
    }

    public function excluir()
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

        // Only veterinario can delete procedimentos
        if ($role !== 'vet') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Sem permissão para excluir procedimentos']);
            return;
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
            return;
        }

        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            // Delete procedimento
            $sql = "DELETE FROM procedimento WHERE id = ? AND fk_veterinario_id = ?";
            
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$id, $_SESSION['id']]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Procedimento excluído com sucesso']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao excluir procedimento']);
            }
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
        }
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

        // Only veterinario can access this page
        if ($role !== 'vet') {
            header('Location: /dashboard');
            die();
        }
    }
}
