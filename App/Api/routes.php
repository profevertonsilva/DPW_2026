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

// --- Solicitações de Adoção + Termo + Avaliação (seções C.4 / C.7) ---
// ATENÇÃO: rotas estáticas (minhas/recebidas) antes de /solicitacoes/{id}.
$router->adicionar('GET',   '/solicitacoes/minhas',          'SolicitacaoController', 'minhas',         true);
$router->adicionar('GET',   '/solicitacoes/recebidas',       'SolicitacaoController', 'recebidas',      true);
$router->adicionar('POST',  '/solicitacoes',                 'SolicitacaoController', 'criar',          true);
$router->adicionar('GET',   '/solicitacoes/{id}',            'SolicitacaoController', 'detalhe',        true);
$router->adicionar('PATCH', '/solicitacoes/{id}/status',     'SolicitacaoController', 'atualizarStatus', true);
$router->adicionar('POST',  '/solicitacoes/{id}/avaliacao',  'SolicitacaoController', 'avaliar',        true);
$router->adicionar('GET',   '/solicitacoes/{id}/termo',      'SolicitacaoController', 'termo',          true);
$router->adicionar('POST',  '/solicitacoes/{id}/termo/assinar', 'SolicitacaoController', 'assinarTermo', true);

// --- Saúde do Animal (seção C.5) — tudo protegido (JWT) ---
$router->adicionar('GET',  '/animais/{id}/vacinas',       'SaudeController', 'vacinas',             true);
$router->adicionar('POST', '/animais/{id}/vacinas',       'SaudeController', 'adicionarVacina',     true);
$router->adicionar('GET',  '/animais/{id}/procedimentos', 'SaudeController', 'procedimentos',       true);
$router->adicionar('POST', '/animais/{id}/procedimentos', 'SaudeController', 'adicionarProcedimento', true);
$router->adicionar('GET',  '/animais/{id}/saude',         'SaudeController', 'saude',               true);
$router->adicionar('PUT',  '/animais/{id}/saude',         'SaudeController', 'atualizarSaude',      true);
$router->adicionar('GET',  '/animais/{id}/carteira',      'SaudeController', 'carteira',            true);
// Auditoria (append-only): mesmo dado de historico_animal, porém protegido (reusa o handler).
$router->adicionar('GET',  '/animais/{id}/auditoria',     'AnimalController', 'historico',          true);
$router->adicionar('GET',  '/vet/atendimentos',           'SaudeController', 'atendimentos',        true);

// --- Avistamentos + Ranking (seção C.6) — protegido ---
$router->adicionar('POST',  '/avistamentos',           'AvistamentoController', 'criar',          true);
$router->adicionar('GET',   '/avistamentos',           'AvistamentoController', 'listar',         true);
$router->adicionar('GET',   '/ranking/rastreadores',   'AvistamentoController', 'ranking',        true);
$router->adicionar('GET',   '/avistamentos/{id}',      'AvistamentoController', 'detalhe',        true);
$router->adicionar('PATCH', '/avistamentos/{id}/status','AvistamentoController', 'atualizarStatus', true);

// --- Taxonomia (seção C.2) ---
$router->adicionar('GET', '/especies', 'AnimalController', 'especies');
$router->adicionar('GET', '/racas',    'AnimalController', 'racas');
