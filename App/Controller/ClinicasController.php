<?php

namespace App\Controller;

use FW\Controller\Action;
use FW\DB\Connection;

class ClinicasController extends Action
{
    protected $clinicas = [];
    protected $clinica = null;

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

        // Only admins can access this page
        if ($role !== 'admin') {
            header('Location: /dashboard');
            die();
        }

        // Load clinics from database
        $clinicas = [];
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            $sql = "SELECT * FROM clinica ORDER BY nome ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $clinicas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error loading clinics: " . $e->getMessage());
        }

        $this->clinicas = $clinicas;
        $this->render('includes/contents/clinicas_listar', 'dashboard');
    }

    public function cadastrar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->validaAutenticacao();

        $tipoUsuario = $_SESSION['tipo_usuario'] ?? 'adotante';

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

        $this->render('includes/contents/clinicas_cadastrar', 'dashboard');
    }

    public function salvar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->validaAutenticacao();

        $tipoUsuario = $_SESSION['tipo_usuario'] ?? 'adotante';

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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $conexao = new Connection();
                $conn = $conexao->getConn();

                $sql = "INSERT INTO clinica (nome, cnpj, cep, logradouro, numero, bairro, cidade, estado, complemento, telefone_1, telefone_2) 
                        VALUES (:nome, :cnpj, :cep, :logradouro, :numero, :bairro, :cidade, :estado, :complemento, :telefone_1, :telefone_2)";

                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':nome' => $_POST['nome'] ?? '',
                    ':cnpj' => $_POST['cnpj'] ?? '',
                    ':cep' => $_POST['cep'] ?? '',
                    ':logradouro' => $_POST['logradouro'] ?? '',
                    ':numero' => $_POST['numero'] ?? '',
                    ':bairro' => $_POST['bairro'] ?? '',
                    ':cidade' => $_POST['cidade'] ?? '',
                    ':estado' => $_POST['estado'] ?? '',
                    ':complemento' => $_POST['complemento'] ?? '',
                    ':telefone_1' => $_POST['telefone_1'] ?? '',
                    ':telefone_2' => $_POST['telefone_2'] ?? ''
                ]);

                header('Location: /clinicas');
                die();
            } catch (\PDOException $e) {
                error_log("Error saving clinic: " . $e->getMessage());
                header('Location: /clinicas-cadastrar?erro=1');
                die();
            }
        }

        header('Location: /clinicas');
        die();
    }

    public function editar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->validaAutenticacao();

        $tipoUsuario = $_SESSION['tipo_usuario'] ?? 'adotante';

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

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /clinicas');
            die();
        }

        // Load clinic from database
        $clinica = null;
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            $sql = "SELECT * FROM clinica WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $clinica = $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error loading clinic: " . $e->getMessage());
        }

        if (!$clinica) {
            header('Location: /clinicas');
            die();
        }

        $this->clinica = $clinica;
        $this->render('includes/contents/clinicas_editar', 'dashboard');
    }

    public function atualizar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->validaAutenticacao();

        $tipoUsuario = $_SESSION['tipo_usuario'] ?? 'adotante';

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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;

            if (!$id) {
                header('Location: /clinicas');
                die();
            }

            try {
                $conexao = new Connection();
                $conn = $conexao->getConn();

                $sql = "UPDATE clinica SET 
                        nome = :nome, 
                        cnpj = :cnpj, 
                        cep = :cep, 
                        logradouro = :logradouro, 
                        numero = :numero, 
                        bairro = :bairro, 
                        cidade = :cidade, 
                        estado = :estado, 
                        complemento = :complemento, 
                        telefone_1 = :telefone_1, 
                        telefone_2 = :telefone_2 
                        WHERE id = :id";

                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':nome' => $_POST['nome'] ?? '',
                    ':cnpj' => $_POST['cnpj'] ?? '',
                    ':cep' => $_POST['cep'] ?? '',
                    ':logradouro' => $_POST['logradouro'] ?? '',
                    ':numero' => $_POST['numero'] ?? '',
                    ':bairro' => $_POST['bairro'] ?? '',
                    ':cidade' => $_POST['cidade'] ?? '',
                    ':estado' => $_POST['estado'] ?? '',
                    ':complemento' => $_POST['complemento'] ?? '',
                    ':telefone_1' => $_POST['telefone_1'] ?? '',
                    ':telefone_2' => $_POST['telefone_2'] ?? '',
                    ':id' => $id
                ]);

                header('Location: /clinicas');
                die();
            } catch (\PDOException $e) {
                error_log("Error updating clinic: " . $e->getMessage());
                header('Location: /clinicas-editar?id=' . $id . '&erro=1');
                die();
            }
        }

        header('Location: /clinicas');
        die();
    }

    public function excluir()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->validaAutenticacao();

        $tipoUsuario = $_SESSION['tipo_usuario'] ?? 'adotante';

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

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /clinicas');
            die();
        }

        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            // Check if clinic has veterinarians
            $sql = "SELECT COUNT(*) as count FROM vet_clinica WHERE fk_clinica_id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                header('Location: /clinicas?erro=has_vets');
                die();
            }

            // Delete clinic
            $sql = "DELETE FROM clinica WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            header('Location: /clinicas');
            die();
        } catch (\PDOException $e) {
            error_log("Error deleting clinic: " . $e->getMessage());
            header('Location: /clinicas?erro=1');
            die();
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
