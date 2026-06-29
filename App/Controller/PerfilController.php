<?php

namespace App\Controller;

use FW\Controller\Action;

class PerfilController extends Action
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->salvarPerfil();
            } catch (\Exception $e) {
                error_log("PerfilController error: " . $e->getMessage());
                $_SESSION['error_message'] = 'Erro ao salvar perfil: ' . $e->getMessage();
                header('Location: /perfil');
                exit;
            }
            return;
        }

        $this->validaAutenticacao();

        $this->render('includes/contents/perfil_content', 'dashboard');
    }

    public function salvar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            $this->salvarPerfil();
        } catch (\Exception $e) {
            error_log("PerfilController salvar error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function salvarPerfil()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        error_log("salvarPerfil called");

        $this->validaAutenticacao();

        $userId = $_SESSION['id'];
        $tipoUsuario = $_SESSION['tipo_usuario'] ?? 'adotante';

        error_log("User ID: $userId, Type: $tipoUsuario");

        // Handle avatar upload
        $avatarPath = $_POST['avatar_atual'] ?? '';
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
            $uploadPath = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
                $avatarPath = '/public/uploads/avatars/' . $filename;
            }
        }

        // Get form data
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $cpf = $_POST['cpf'] ?? '';
        $rg = $_POST['rg'] ?? '';
        $endereco = $_POST['endereco'] ?? '';
        $cidade = $_POST['cidade'] ?? '';
        $estado = $_POST['estado'] ?? '';
        $cep = $_POST['cep'] ?? '';
        $bio = $_POST['bio'] ?? '';

        error_log("Form data received: nome=$nome, email=$email");

        try {
            if ($tipoUsuario === 'adotante') {
                $dao = new \App\DAO\AdotanteDAO();
                $model = $dao->buscarPorId($userId);
                if ($model) {
                    $model->__set('nome', $nome);
                    $model->__set('telefone_1', $telefone);
                    $model->__set('cpf', $cpf);
                    $model->__set('bio', $bio);
                    $model->__set('avatar', $avatarPath);
                    $dao->alterar($model);
                }
            } elseif ($tipoUsuario === 'administrador') {
                $dao = new \App\DAO\AdministradorDAO();
                $model = $dao->buscarPorId($userId);
                if ($model) {
                    $model->__set('nome', $nome);
                    $model->__set('telefone', $telefone);
                    $model->__set('cpf', $cpf);
                    $model->__set('bio', $bio);
                    $model->__set('avatar', $avatarPath);
                    $dao->alterar($model);
                }
            } elseif ($tipoUsuario === 'veterinario') {
                $dao = new \App\DAO\VeterinarioDAO();
                $model = $dao->buscarPorId($userId);
                if ($model) {
                    $model->__set('nome', $nome);
                    $model->__set('telefone', $telefone);
                    $model->__set('cpf', $cpf);
                    $model->__set('bio', $bio);
                    $model->__set('avatar', $avatarPath);
                    $dao->alterar($model);
                }
            } elseif ($tipoUsuario === 'moderador') {
                $dao = new \App\DAO\RastreadorDAO();
                $model = $dao->buscarPorId($userId);
                if ($model) {
                    $model->__set('nome', $nome);
                    $model->__set('telefone_1', $telefone);
                    $model->__set('cpf', $cpf);
                    $model->__set('bio', $bio);
                    $model->__set('avatar', $avatarPath);
                    $dao->alterar($model);
                }
            } elseif ($tipoUsuario === 'ong') {
                // OngDAO doesn't exist, skip for now
            }

            $_SESSION['success_message'] = 'Perfil atualizado com sucesso!';
            error_log("Profile save successful");
        } catch (\Exception $e) {
            error_log("Profile save error: " . $e->getMessage());
            $_SESSION['error_message'] = 'Erro ao atualizar perfil: ' . $e->getMessage();
        }

        error_log("Redirecting to /perfil");
        header('Location: /perfil');
        exit;
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
