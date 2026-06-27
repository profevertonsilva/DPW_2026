<?php

/**
 * Tabela de rotas da API REST (prefixo /api, adicionado pelo index.php).
 *
 * Uso: $router->adicionar(MÉTODO, CAMINHO, Controller, ação, protegida?);
 *   - CAMINHO aceita parâmetros no formato {nome} (ex.: '/animais/{id}').
 *   - protegida = true exige JWT válido (Authorization: Bearer {token}).
 *
 * Esta é a BASE da API: apenas endpoints de saúde/diagnóstico. Os módulos de
 * negócio (auth, animais, solicitações, ...) serão adicionados aqui depois.
 *
 * @var \App\Api\ApiRouter $router
 */

// Diagnóstico — públicos
$router->adicionar('GET', '/ping',   'HealthController', 'ping');
$router->adicionar('GET', '/health', 'HealthController', 'health');

// Verificação do fluxo de JWT — protegido (exige token válido)
$router->adicionar('GET', '/me', 'HealthController', 'me', true);

// --- Auth (seção C.1) ---
$router->adicionar('POST', '/auth/login',                 'AuthController', 'login');
$router->adicionar('POST', '/auth/logout',                'AuthController', 'logout', true);
$router->adicionar('POST', '/auth/cadastrar/adotante',    'AuthController', 'cadastrarAdotante');
$router->adicionar('POST', '/auth/cadastrar/ong',         'AuthController', 'cadastrarOng');
$router->adicionar('POST', '/auth/cadastrar/veterinario', 'AuthController', 'cadastrarVeterinario');
$router->adicionar('POST', '/auth/alterar-senha',         'AuthController', 'alterarSenha', true);
$router->adicionar('POST', '/auth/recuperar-senha',       'AuthController', 'recuperarSenha');

// --- Animais (seção C.2) ---
// ATENÇÃO: /animais/meus DEVE vir antes de /animais/{id} para não ser capturado como id.
$router->adicionar('GET',  '/animais',                'AnimalController', 'listar');
$router->adicionar('GET',  '/animais/meus',           'AnimalController', 'meus',      true);
$router->adicionar('GET',  '/animais/{id}',           'AnimalController', 'detalhe');
$router->adicionar('GET',  '/animais/{id}/historico', 'AnimalController', 'historico');
$router->adicionar('POST', '/animais',                'AnimalController', 'criar',     true);
$router->adicionar('PUT',  '/animais/{id}',           'AnimalController', 'atualizar', true);

// --- ONGs (seção C.3) ---
$router->adicionar('GET', '/ongs',                'OngController', 'listar');
$router->adicionar('GET', '/ongs/{id}',           'OngController', 'detalhe');
$router->adicionar('GET', '/ongs/{id}/animais',   'OngController', 'animais');

// --- Taxonomia (seção C.2) ---
$router->adicionar('GET', '/especies', 'AnimalController', 'especies');
$router->adicionar('GET', '/racas',    'AnimalController', 'racas');
