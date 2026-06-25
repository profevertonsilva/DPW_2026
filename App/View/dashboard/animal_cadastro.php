<?php
/**
 * AmigoPet - Módulo de Animais (Cadastro)

 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclui a moldura global usando caminhos absolutos indestrutíveis
include __DIR__ . '/../includes/dashboard/header.php';
include __DIR__ . '/../includes/dashboard/navbar.php';
include __DIR__ . '/../includes/dashboard/menu.php';

// Injeta o formulário com a identidade visual obrigatória
include __DIR__ . '/animal_cadastro_content.php'; 

include __DIR__ . '/../includes/dashboard/footer.php';