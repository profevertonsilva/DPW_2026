<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\LoginDAO;
use FW\DB\Connection;

class UsuarioController extends Action
{
    public function index()
    {
        $this->getView()->title = 'Gerenciar Usuários';
        $this->getView()->title_pagina = 'Gerenciamento de Usuários';

        $this->render('includes/contents/usuarios_content', 'dashboard');
    }

    public function atualizarCargo()
    {
        header('Content-Type: application/json');

        $id = $_POST['id'] ?? null;
        $tipoUsuario = $_POST['tipo_usuario'] ?? null;

        // Log para debug
        error_log("Controller recebido: ID=$id, Tipo=$tipoUsuario, POST=" . print_r($_POST, true));

        if (!$id || !$tipoUsuario) {
            echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
            exit;
        }

        try {
            $loginDAO = new LoginDAO();
            $resultado = $loginDAO->atualizarTipoUsuario($id, $tipoUsuario);

            if ($resultado) {
                // Verify the update actually happened
                $conexao = new Connection();
                $conn = $conexao->getConn();
                $checkSql = "SELECT tipo_usuario FROM login WHERE id = :id";
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->execute([':id' => $id]);
                $checkResult = $checkStmt->fetch(\PDO::FETCH_ASSOC);
                
                error_log("Verificação pós-atualização: ID=$id, Tipo no banco=" . ($checkResult['tipo_usuario'] ?? 'NULL') . ", Tipo esperado=$tipoUsuario");
                
                if ($checkResult['tipo_usuario'] === $tipoUsuario) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Atualização não persistiu no banco']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar cargo - verifique o log de erros do PHP']);
            }
        } catch (\Exception $e) {
            error_log("Exceção no controller: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function vincularONG()
    {
        header('Content-Type: application/json');

        $id = $_POST['id'] ?? null;
        $ongId = $_POST['fk_ong_id'] ?? null;

        if (!$id || !$ongId) {
            echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
            exit;
        }

        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            // Backend schema doesn't have fk_ong_id in login table
            // Use ong_animal table to track which ONG a user is linked to
            // We'll create a dummy animal entry or use the ong_animal table as a linkage tracker

            // Check if ONG record exists
            $sql = "SELECT id FROM ong WHERE id = :ong_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':ong_id' => $ongId]);
            $ongExists = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$ongExists) {
                echo json_encode(['success' => false, 'message' => 'ONG não encontrada']);
                exit;
            }

            // Update user's tipo_usuario to 'ong'
            $sql = "UPDATE login SET tipo_usuario = 'ong' WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            // Store the linkage in ong_animal table as a tracker (using user_id as fk_animal_id temporarily)
            // This is a workaround since fk_ong_id doesn't exist in login table
            // First, remove any existing linkage for this user
            $sql = "DELETE FROM ong_animal WHERE fk_animal_id = :user_id AND fk_animal_id NOT IN (SELECT id FROM animal)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':user_id' => $id]);

            // Insert new linkage tracker
            $sql = "INSERT INTO ong_animal (fk_ong_id, fk_animal_id) VALUES (:ong_id, :user_id)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':ong_id' => $ongId, ':user_id' => $id]);

            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            error_log("Error linking ONG: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function vincularClinica()
    {
        header('Content-Type: application/json');

        $id = $_POST['id'] ?? null;
        $clinicaId = $_POST['fk_clinica_id'] ?? null;

        if (!$id || !$clinicaId) {
            echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
            exit;
        }

        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            // Check if vet_clinica record exists, if not create it
            $sql = "SELECT id FROM vet_clinica WHERE fk_veterinario_id = :vet_id AND fk_clinica_id = :clinica_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':vet_id' => $id, ':clinica_id' => $clinicaId]);
            $existing = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$existing) {
                // Create vet_clinica relationship
                $sql = "INSERT INTO vet_clinica (fk_veterinario_id, fk_clinica_id) VALUES (:vet_id, :clinica_id)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':vet_id' => $id, ':clinica_id' => $clinicaId]);
            }

            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            error_log("Error linking clinic: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function validaAutenticacao()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /login');
            die();
        }

        // Check if user is administrator for usuarios page
        if ($_SESSION['tipo_usuario'] !== 'administrador') {
            header('Location: /dashboard');
            die();
        }
    }
}
