<?php
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);

$dotenv->load();

// Requisições /api/* vão para a API REST (JSON/JWT, stateless — sem sessão).
$caminho = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
if (preg_match('#^/api(/|$)#', $caminho)) {
    $router = new \App\Api\ApiRouter();
    require __DIR__ . '/App/Api/routes.php';
    $router->despachar();
    return;
}

// Demais requisições seguem para a aplicação web (sessão/HTML).
session_start();

$route = new \App\Route();
