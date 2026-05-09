<?php
/**
 * AmigoPet - Header Component
 * Localização: ~/App/View/Includes/dashboard/header.php
 */

// 1. Inicia a sessão no topo de tudo
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

/** * 2. LÓGICA DE SIMULAÇÃO DE CARGO
 * Movida para cá para que o menu.php (carregado depois) já receba o valor correto.
 */
if (isset($_GET['sim_role'])) {
    $_SESSION['sim_user_role'] = $_GET['sim_role'];
    
    // Remove o parâmetro da URL e recarrega para limpar o estado
    $clean_url = strtok($_SERVER["REQUEST_URI"], '?');
    header("Location: " . $clean_url);
    exit;
}

// Define o cargo padrão caso não exista
$userRole = $_SESSION['sim_user_role'] ?? 'usuario';

$siteNome = "AmigoPet";
$siteTitulo = "Painel Administrativo";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $siteNome . " - " . $siteTitulo; ?></title>

    <!-- Fontes: Poppins e Inter -->
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