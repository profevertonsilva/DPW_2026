<?php

namespace App\Controller;

use FW\Controller\Action;
use FW\DB\Connection;

class HistoricoMedicoController extends Action
{
    protected $historico = [];

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

        // Only veterinarians and admins can access this page
        if ($role !== 'vet' && $role !== 'admin') {
            header('Location: /dashboard');
            die();
        }

        // Get search term from GET parameter
        $searchTerm = $_GET['search'] ?? '';

        // Load evaluated cases from database
        $historico = [];
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            // Build query with search filter
            $sql = "SELECT cv.*, a.nome as animal_nome, e.nome as especie, r.nome as raca, o.nome as ong_nome
                    FROM caso_veterinario cv
                    LEFT JOIN animal a ON cv.fk_animal_id = a.id
                    LEFT JOIN especie e ON a.fk_especie_id = e.id
                    LEFT JOIN animal_raca ar ON a.id = ar.fk_animal_id
                    LEFT JOIN raca r ON ar.fk_raca_id = r.id
                    LEFT JOIN ong o ON cv.fk_ong_id = o.id
                    WHERE cv.diagnostico IS NOT NULL";

            // Add search conditions if search term is provided
            if ($searchTerm) {
                $sql .= " AND (
                    a.nome LIKE :search OR
                    e.nome LIKE :search OR
                    r.nome LIKE :search OR
                    cv.diagnostico LIKE :search OR
                    cv.tratamento LIKE :search OR
                    o.nome LIKE :search
                )";
            }

            $sql .= " ORDER BY cv.data_avaliacao DESC";

            $stmt = $conn->prepare($sql);

            if ($searchTerm) {
                $searchParam = '%' . $searchTerm . '%';
                $stmt->bindValue(':search', $searchParam);
            }

            $stmt->execute();
            $historico = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Log error but continue with empty array
            error_log("Error loading historico: " . $e->getMessage());
        }

        // Pass historico and search term to the view
        $this->historico = $historico;
        $this->render('includes/contents/historico_medico_content', 'dashboard');
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
