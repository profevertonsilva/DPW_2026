<?php

namespace App\Controller;

use FW\Controller\Action;
use FW\DB\Connection;

class EnviarCasoVeterinarioController extends Action
{
    protected $animais = [];
    protected $casosEnviados = [];

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

        // Only ONGs and admins can access this page
        if ($role !== 'ong' && $role !== 'admin') {
            header('Location: /dashboard');
            die();
        }

        // Load animals from database (ONG's animals)
        $animais = [];
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            // Query with joins to get species and breed names
            $sql = "SELECT a.*, e.nome as especie, r.nome as raca
                    FROM animal a
                    LEFT JOIN especie e ON a.fk_especie_id = e.id
                    LEFT JOIN animal_raca ar ON a.id = ar.fk_animal_id
                    LEFT JOIN raca r ON ar.fk_raca_id = r.id
                    ORDER BY a.nome ASC";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $animais = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Log error but continue with empty array
            error_log("Error loading animais: " . $e->getMessage());
        }

        // Load ONG's sent cases with evaluation details
        $casosEnviados = [];
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            // Get ONG ID from session
            $fk_ong_id = $_SESSION['id'] ?? null;

            if ($fk_ong_id) {
                $sql = "SELECT cv.*, a.nome as animal_nome, e.nome as especie, r.nome as raca
                        FROM caso_veterinario cv
                        LEFT JOIN animal a ON cv.fk_animal_id = a.id
                        LEFT JOIN especie e ON a.fk_especie_id = e.id
                        LEFT JOIN animal_raca ar ON a.id = ar.fk_animal_id
                        LEFT JOIN raca r ON ar.fk_raca_id = r.id
                        WHERE cv.fk_ong_id = :fk_ong_id
                        ORDER BY cv.data_envio DESC";

                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':fk_ong_id', $fk_ong_id);
                $stmt->execute();
                $casosEnviados = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        } catch (\PDOException $e) {
            // Log error but continue with empty array
            error_log("Error loading casos enviados: " . $e->getMessage());
        }

        // Pass animais and casosEnviados to the view using protected properties
        $this->animais = $animais;
        $this->casosEnviados = $casosEnviados;
        $this->render('includes/contents/enviar_caso_veterinario_content', 'dashboard');
    }

    public function salvar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->validaAutenticacao();

        header('Content-Type: application/json');

        $fk_animal_id = $_POST['fk_animal_id'] ?? null;
        $prioridade = $_POST['prioridade'] ?? null;
        $descricao = $_POST['descricao'] ?? null;
        $observacoes = $_POST['observacoes'] ?? null;

        if (!$fk_animal_id || !$prioridade || !$descricao) {
            echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não preenchidos']);
            return;
        }

        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            // Get ONG ID from session (assuming ONG ID matches login ID for ONG users)
            $fk_ong_id = $_SESSION['id'] ?? null;

            $sql = "INSERT INTO caso_veterinario (fk_animal_id, fk_ong_id, prioridade, descricao, observacoes, status, data_envio)
                    VALUES (:fk_animal_id, :fk_ong_id, :prioridade, :descricao, :observacoes, 'pendente', NOW())";

            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':fk_animal_id', $fk_animal_id);
            $stmt->bindValue(':fk_ong_id', $fk_ong_id);
            $stmt->bindValue(':prioridade', $prioridade);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':observacoes', $observacoes);
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'Caso enviado com sucesso']);
        } catch (\PDOException $e) {
            error_log("Error saving caso: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar caso: ' . $e->getMessage()]);
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
