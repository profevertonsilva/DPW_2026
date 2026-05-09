<?php
/**
 * AmigoPet - Página de Gerenciamento de Usuários
 * Localização: ~/App/View/usuarios.php
 */

include 'includes/dashboard/header.php';
include 'includes/dashboard/menu.php';
include 'includes/dashboard/navbar.php';

/**
 * CONTROLE DE ACESSO:
 * Apenas administradores ou moderadores podem acessar esta página.
 */
$role = $_SESSION['sim_user_role'] ?? 'usuario';
if (!in_array($role, ['admin', 'moderador'])) {
    echo "<script>window.location.href='dashboard.php';</script>";
    exit;
}

include 'usuarios_content.php'; 

include 'includes/dashboard/footer.php';
?>