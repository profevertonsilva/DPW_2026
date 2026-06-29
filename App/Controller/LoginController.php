<?php

namespace App\Controller;

use App\DAO\LoginDAO;
use App\Model\LoginModel;
use FW\Controller\Action;

class LoginController extends Action
{

    public function autenticar()
    {

        if (!isset($_POST['email']) || !isset($_POST['senha'])) {
            header('Location: /?erro=1');
            die();
        }

        $email = trim($_POST['email']);
        $senha = $_POST['senha'];

        $loginDAO = new LoginDAO();
        $login = $loginDAO->buscarPorEmail($email);

        if (!$login) {
            header('Location: /?erro=3');
            die();
        }

        $senhaSalva = $login->__get('senha');

        if (!password_verify($senha, $senhaSalva)) {
            header('Location: /?erro=1');
            die();
        }

        if ($login->__get('status') !== 'a') {
            header('Location: /?erro=2');
            die();
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION['id'] = $login->__get('id');
        $_SESSION['email'] = $login->__get('email');
        $_SESSION['tipo_usuario'] = $login->__get('tipo_usuario');
        $_SESSION['status'] = $login->__get('status');

        // Fetch user's name from appropriate table based on tipo_usuario
        $tipoUsuario = $login->__get('tipo_usuario');
        $loginId = $login->__get('id');
        $nomeUsuario = 'Usuário';

        error_log("Login attempt - tipo_usuario: $tipoUsuario, login_id: $loginId");

        try {
            $conn = $loginDAO->getConn();
            if (!$conn) {
                error_log("Database connection is null in LoginController");
            } else {
                switch ($tipoUsuario) {
                    case 'adotante':
                        $stmt = $conn->prepare("SELECT nome FROM adotante WHERE fk_login_id = :login_id");
                        $stmt->bindValue(':login_id', $loginId);
                        $stmt->execute();
                        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                        if ($result) {
                            $nomeUsuario = $result['nome'];
                            error_log("Found adotante name: $nomeUsuario");
                        } else {
                            error_log("No adotante found for login_id: $loginId");
                        }
                        break;
                    case 'ong':
                        // ONG table doesn't have fk_login_id, use id directly
                        $stmt = $conn->prepare("SELECT nome FROM ong WHERE id = :login_id");
                        $stmt->bindValue(':login_id', $loginId);
                        $stmt->execute();
                        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                        if ($result) {
                            $nomeUsuario = $result['nome'];
                            error_log("Found ong name: $nomeUsuario");
                        } else {
                            error_log("No ong found for id: $loginId, using email as name");
                            // Use email as fallback
                            $nomeUsuario = $login->__get('email');
                        }
                        break;
                    case 'veterinario':
                        $stmt = $conn->prepare("SELECT nome FROM veterinario WHERE fk_login_id = :login_id");
                        $stmt->bindValue(':login_id', $loginId);
                        $stmt->execute();
                        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                        if ($result) {
                            $nomeUsuario = $result['nome'];
                            error_log("Found veterinario name: $nomeUsuario");
                        } else {
                            error_log("No veterinario found for login_id: $loginId, trying alternative approach");
                            $stmt2 = $conn->prepare("SELECT v.nome FROM veterinario v JOIN login l ON v.id = l.id WHERE l.id = :login_id");
                            $stmt2->bindValue(':login_id', $loginId);
                            $stmt2->execute();
                            $result2 = $stmt2->fetch(\PDO::FETCH_ASSOC);
                            if ($result2) {
                                $nomeUsuario = $result2['nome'];
                                error_log("Found veterinario name via join: $nomeUsuario");
                            } else {
                                // Use email as fallback
                                $nomeUsuario = $login->__get('email');
                                error_log("No veterinario found via join for login_id: $loginId, using email as name: $nomeUsuario");
                            }
                        }
                        break;
                    case 'administrador':
                        // Administrador table doesn't have fk_login_id, try joining by id
                        $stmt = $conn->prepare("SELECT a.nome FROM administrador a JOIN login l ON a.id = l.id WHERE l.id = :login_id");
                        $stmt->bindValue(':login_id', $loginId);
                        $stmt->execute();
                        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                        if ($result) {
                            $nomeUsuario = $result['nome'];
                            error_log("Found administrador name via join: $nomeUsuario");
                        } else {
                            // Use email as fallback if no record found
                            $nomeUsuario = $login->__get('email');
                            error_log("No administrador found via join for login_id: $loginId, using email as name: $nomeUsuario");
                        }
                        break;
                    case 'moderador':
                        $stmt = $conn->prepare("SELECT r.nome FROM rastreador r JOIN login l ON r.id = l.id WHERE l.id = :login_id");
                        $stmt->bindValue(':login_id', $loginId);
                        $stmt->execute();
                        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                        if ($result) {
                            $nomeUsuario = $result['nome'];
                            error_log("Found rastreador name via join: $nomeUsuario");
                        } else {
                            // Use email as fallback
                            $nomeUsuario = $login->__get('email');
                            error_log("No rastreador found via join for login_id: $loginId, using email as name: $nomeUsuario");
                        }
                        break;
                    default:
                        error_log("Unknown tipo_usuario: $tipoUsuario");
                }
            }
        } catch (\Exception $e) {
            error_log("Error fetching user name: " . $e->getMessage());
            $nomeUsuario = 'Usuário';
        }

        error_log("Setting session nome: $nomeUsuario");
        $_SESSION['nome'] = $nomeUsuario;

        header('Location: /dashboard');
        die();
    }

    public function alterarSenha()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
            header('Location: /login?erro=2');
            die();
        }

        $senhaAtual = $_POST["senha_atual"] ?? '';
        $senhaNova = $_POST["senha_nova"] ?? '';
        $senhaConfirmacao = $_POST["senha_confirmacao"] ?? '';

        if (empty($senhaAtual) || empty($senhaNova) || empty($senhaConfirmacao)) {
            header('Location:/perfil/alterarSenha?erro=1');
            die();
        }

        if (!($senhaNova !== $senhaConfirmacao)) {
            header('Location: /perfil/alterarSenha?erro=2');
            die();
        }

        if (strlen($senhaNova) < 8) {
            header('Location: /perfil/alterarSenha?erro=3');
            die();
        }

        $loginDAO = new LoginDAO();
        $login = $loginDAO->buscarPorId($_SESSION['id']);

        if (!$login) {
            header('Location:/perfil/alterarSenha?erro=4');
            die();
        }

        $senhaSalva = $login->__get('senha');

        if (!password_verify($senhaAtual, $senhaSalva)) {
            // TODO: criar view /perfil/alterarSenha
            header('Location: /perfil/alterarSenha?erro=5');
            die();
        }



        $loginModel = new LoginModel();
        $loginModel->__set('id', $_SESSION['id']);
        $loginModel->__set('senha', $senhaNova);

        $loginDAO->alterarSenha($loginModel);

        header('Location: /perfil?senha=alterada');
        die();
    }

    public function logout()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        session_destroy();
        header('Location: /');
        die();
    }

    public function validaAutenticacao()
    {
        // Rota pública, não requer autenticação
    }
}
