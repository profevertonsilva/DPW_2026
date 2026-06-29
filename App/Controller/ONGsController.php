<?php

namespace App\Controller;

use FW\Controller\Action;
use FW\DB\Connection;

class ONGsController extends Action
{
    protected $ongs = [];
    protected $ong = null;

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

        // Load ONGs from database
        $ongs = [];
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            $sql = "SELECT * FROM ong ORDER BY nome ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $ongs = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error loading ONGs: " . $e->getMessage());
        }

        $this->ongs = $ongs;
        $this->render('includes/contents/ongs_listar', 'dashboard');
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

        $this->render('includes/contents/ongs_cadastrar', 'dashboard');
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

                $sql = "INSERT INTO ong (nome, cnpj, cep, logradouro, bairro, cidade, estado, complemento, telefone_1, telefone_2, quantidade_animais, status) 
                        VALUES (:nome, :cnpj, :cep, :logradouro, :bairro, :cidade, :estado, :complemento, :telefone_1, :telefone_2, :quantidade_animais, :status)";

                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':nome' => $_POST['nome'] ?? '',
                    ':cnpj' => $_POST['cnpj'] ?? '',
                    ':cep' => $_POST['cep'] ?? '',
                    ':logradouro' => $_POST['logradouro'] ?? '',
                    ':bairro' => $_POST['bairro'] ?? '',
                    ':cidade' => $_POST['cidade'] ?? '',
                    ':estado' => $_POST['estado'] ?? '',
                    ':complemento' => $_POST['complemento'] ?? '',
                    ':telefone_1' => $_POST['telefone_1'] ?? '',
                    ':telefone_2' => $_POST['telefone_2'] ?? '',
                    ':quantidade_animais' => 0,
                    ':status' => 'a'
                ]);

                header('Location: /ongs');
                die();
            } catch (\PDOException $e) {
                error_log("Error saving ONG: " . $e->getMessage());
                header('Location: /ongs-cadastrar?erro=1');
                die();
            }
        }

        header('Location: /ongs');
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
            header('Location: /ongs');
            die();
        }

        // Load ONG from database
        $ong = null;
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            $sql = "SELECT * FROM ong WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $ong = $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error loading ONG: " . $e->getMessage());
        }

        if (!$ong) {
            header('Location: /ongs');
            die();
        }

        $this->ong = $ong;
        $this->render('includes/contents/ongs_editar', 'dashboard');
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
                header('Location: /ongs');
                die();
            }

            try {
                $conexao = new Connection();
                $conn = $conexao->getConn();

                $sql = "UPDATE ong SET 
                        nome = :nome, 
                        cnpj = :cnpj, 
                        cep = :cep, 
                        logradouro = :logradouro, 
                        bairro = :bairro, 
                        cidade = :cidade, 
                        estado = :estado, 
                        complemento = :complemento, 
                        telefone_1 = :telefone_1, 
                        telefone_2 = :telefone_2, 
                        status = :status 
                        WHERE id = :id";

                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':nome' => $_POST['nome'] ?? '',
                    ':cnpj' => $_POST['cnpj'] ?? '',
                    ':cep' => $_POST['cep'] ?? '',
                    ':logradouro' => $_POST['logradouro'] ?? '',
                    ':bairro' => $_POST['bairro'] ?? '',
                    ':cidade' => $_POST['cidade'] ?? '',
                    ':estado' => $_POST['estado'] ?? '',
                    ':complemento' => $_POST['complemento'] ?? '',
                    ':telefone_1' => $_POST['telefone_1'] ?? '',
                    ':telefone_2' => $_POST['telefone_2'] ?? '',
                    ':status' => $_POST['status'] ?? 'a',
                    ':id' => $id
                ]);

                header('Location: /ongs');
                die();
            } catch (\PDOException $e) {
                error_log("Error updating ONG: " . $e->getMessage());
                header('Location: /ongs-editar?id=' . $id . '&erro=1');
                die();
            }
        }

        header('Location: /ongs');
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
            header('Location: /ongs');
            die();
        }

        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();

            // Check if ONG has animals
            $sql = "SELECT COUNT(*) as count FROM ong_animal WHERE fk_ong_id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                header('Location: /ongs?erro=has_animals');
                die();
            }

            // Delete ONG
            $sql = "DELETE FROM ong WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            header('Location: /ongs');
            die();
        } catch (\PDOException $e) {
            error_log("Error deleting ONG: " . $e->getMessage());
            header('Location: /ongs?erro=1');
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
