<?php

namespace App\Controller;

use FW\Controller\Action;
use FW\DB\Connection;

class CasosVeterinarioController extends Action
{
    protected $casos = [];

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

        // Only veterinarians can access this page
        if ($role !== 'vet') {
            header('Location: /dashboard');
            die();
        }

        // Load casos from database
        $casos = [];
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            $sql = "SELECT cv.*, a.nome as animal_nome, e.nome as especie, r.nome as raca, o.nome as ong_nome
                    FROM caso_veterinario cv 
                    LEFT JOIN animal a ON cv.fk_animal_id = a.id
                    LEFT JOIN especie e ON a.fk_especie_id = e.id
                    LEFT JOIN animal_raca ar ON a.id = ar.fk_animal_id
                    LEFT JOIN raca r ON ar.fk_raca_id = r.id
                    LEFT JOIN ong o ON cv.fk_ong_id = o.id
                    ORDER BY cv.data_envio DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $casos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Log error but continue with empty array
            error_log("Error loading casos: " . $e->getMessage());
        }

        // Pass casos to the view using protected property
        $this->casos = $casos;
        $this->render('includes/contents/casos_veterinario_content', 'dashboard');
    }

    public function detalhes()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->validaAutenticacao();

        header('Content-Type: application/json');

        $casoId = $_GET['id'] ?? null;

        if (!$casoId) {
            echo json_encode(['success' => false, 'message' => 'ID do caso não fornecido']);
            return;
        }

        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            $sql = "SELECT cv.*, a.nome as animal_nome, e.nome as especie, r.nome as raca, o.nome as ong_nome
                    FROM caso_veterinario cv 
                    LEFT JOIN animal a ON cv.fk_animal_id = a.id
                    LEFT JOIN especie e ON a.fk_especie_id = e.id
                    LEFT JOIN animal_raca ar ON a.id = ar.fk_animal_id
                    LEFT JOIN raca r ON ar.fk_raca_id = r.id
                    LEFT JOIN ong o ON cv.fk_ong_id = o.id
                    WHERE cv.id = :caso_id";

            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':caso_id', $casoId);
            $stmt->execute();
            $caso = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($caso) {
                echo json_encode(['success' => true, 'caso' => $caso]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Caso não encontrado']);
            }
        } catch (\PDOException $e) {
            error_log("Error loading caso details: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erro ao carregar detalhes: ' . $e->getMessage()]);
        }
    }

    public function salvar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->validaAutenticacao();

        header('Content-Type: application/json');

        $caso_id = $_POST['caso_id'] ?? null;
        $diagnostico = $_POST['diagnostico'] ?? null;
        $tratamento = $_POST['tratamento'] ?? null;
        $status = $_POST['status'] ?? null;
        $prioridade = $_POST['prioridade'] ?? null;
        $observacoes = $_POST['observacoes'] ?? null;

        if (!$caso_id || !$diagnostico || !$tratamento || !$status) {
            echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não preenchidos']);
            return;
        }

        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            $sql = "UPDATE caso_veterinario 
                    SET diagnostico = :diagnostico, 
                        tratamento = :tratamento, 
                        status = :status, 
                        prioridade = :prioridade, 
                        observacoes = :observacoes,
                        data_avaliacao = NOW(),
                        data_atualizacao = NOW()
                    WHERE id = :caso_id";

            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':diagnostico', $diagnostico);
            $stmt->bindValue(':tratamento', $tratamento);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':prioridade', $prioridade);
            $stmt->bindValue(':observacoes', $observacoes);
            $stmt->bindValue(':caso_id', $caso_id);
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'Avaliação salva com sucesso']);
        } catch (\PDOException $e) {
            error_log("Error saving avaliacao: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar avaliação: ' . $e->getMessage()]);
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
    }
}
