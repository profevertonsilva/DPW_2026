<?php
/**
 * AmigoPet - Header Component
 * Localização: ~/App/View/Includes/dashboard/header.php
 */

// 1. Inicia a sessão no topo de tudo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define o cargo real do usuário
$tipoUsuario = $_SESSION['tipo_usuario'] ?? 'adotante';

// Mapeia o tipo_usuario do banco para os roles do menu
$roleMap = [
    'administrador' => 'admin',
    'ong' => 'ong',
    'veterinario' => 'vet',
    'moderador' => 'campo',
    'adotante' => 'usuario'
];

$userRole = $roleMap[$tipoUsuario] ?? 'usuario';

$siteNome = "AmigoPet";
$siteTitulo = "Painel Administrativo";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="/">
    <title><?php echo $siteNome . " - " . $siteTitulo; ?></title>

    <!-- Fontes: Poppins e Inter -->
    <script src="https://kit.fontawesome.com/b7dcac0188.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap & Styles -->
    <link rel="stylesheet" href="/resources/dashboard/css/bootstrap.css">
    <link rel="stylesheet" href="/resources/dashboard/css/style.css">

    <!-- Scripts -->
    <script src="/resources/dashboard/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    

</head>
<body class="bg-off-white">