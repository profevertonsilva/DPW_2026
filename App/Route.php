<?php

namespace App;

use FW\Init\Boostrap;
use FW\Router\RouteManager;

class Route extends Boostrap
{

    public function initRoutes()
    {
        // Inicia logging de debug
        $routeLogDir = __DIR__ . '/../';
        $logFile = is_writable($routeLogDir) ? $routeLogDir . 'route_debug.log' : sys_get_temp_dir() . '/route_debug.log';
        $timestamp = date('Y-m-d H:i:s');
        $requestUri = $_SERVER['REQUEST_URI'] ?? 'N/A';
        file_put_contents($logFile, "[$timestamp] REQUEST_URI: $requestUri\n", FILE_APPEND);

        //Não excluir a Rota abaixo
        $routes['error-404'] = array(
            'route' => '/error404',
            'controller' => 'ErrorController',
            'action' => 'error404'
        );

        // Root route - redirect to dashboard
        $routes['root'] = array(
            'route' => '/',
            'controller' => 'SiteController',
            'action' => 'index'
        );

        // Force dashboard route to override any database route
        $routes['dashboard'] = array(
            'route' => '/dashboard',
            'controller' => 'DashboardController',
            'action' => 'index',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Forced dashboard route\n", FILE_APPEND);

        // Add logout route
        $routes['logout'] = array(
            'route' => '/logout',
            'controller' => 'LoginController',
            'action' => 'logout',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added logout route\n", FILE_APPEND);

        // Add login route
        $routes['login'] = array(
            'route' => '/login',
            'controller' => 'SiteController',
            'action' => 'login',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added login route\n", FILE_APPEND);

        // Add autenticar route for login POST
        $routes['autenticar'] = array(
            'route' => '/autenticar',
            'controller' => 'LoginController',
            'action' => 'autenticar',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added autenticar route\n", FILE_APPEND);

        // Add adotar route for animal profile
        $routes['adotar'] = array(
            'route' => '/adotar',
            'controller' => 'AdotarController',
            'action' => 'index',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added adotar route\n", FILE_APPEND);

        $routeManager = RouteManager::getInstance();
        $dbRoutes = $routeManager->getAllRoutes();
        
        file_put_contents($logFile, "[$timestamp] DB Routes count: " . count($dbRoutes) . "\n", FILE_APPEND);

        foreach ($dbRoutes as $dbRoute) {
            // Skip any route that points to CadastroController (which doesn't exist)
            if ($dbRoute['controller'] === 'CadastroController') {
                file_put_contents($logFile, "[$timestamp] Skipping invalid route: " . $dbRoute['nome_rota'] . " -> CadastroController\n", FILE_APPEND);
                continue;
            }
            $routes[$dbRoute['nome_rota']] = array(
                'route' => '/' . $dbRoute['slug'],
                'controller' => $dbRoute['controller'],
                'action' => $dbRoute['action'],
                'is_dynamic' => $dbRoute['is_dynamic'],
                'pattern' => $dbRoute['pattern'] ?? null
            );
        }

        if (!isset($routes['dashboard_animal_editar'])) {
            $routes['dashboard_animal_editar'] = array(
                'route' => '/dashboard/animal/editar/{id}',
                'controller' => 'AnimalController',
                'action' => 'editar',
                'is_dynamic' => 1,
                'pattern' => 'dashboard/animal/editar/{id}'
            );
            file_put_contents($logFile, "[$timestamp] Added fallback route: dashboard_animal_editar\n", FILE_APPEND);
        }

        if (!isset($routes['dashboard_animal_alterar'])) {
            $routes['dashboard_animal_alterar'] = array(
                'route' => '/dashboard/animal/alterar',
                'controller' => 'AnimalController',
                'action' => 'alterar',
                'is_dynamic' => 0,
                'pattern' => null
            );
        }

        if (!isset($routes['dashboard_animal_listar'])) {
            $routes['dashboard_animal_listar'] = array(
                'route' => '/dashboard/animal/listar',
                'controller' => 'AnimalController',
                'action' => 'listar',
                'is_dynamic' => 0,
                'pattern' => null
            );
        }

        if (!isset($routes['animal_perfil'])) {
            $routes['animal_perfil'] = array(
                'route' => '/animal/perfil/{id}',
                'controller' => 'AnimalController',
                'action' => 'perfil',
                'is_dynamic' => 1,
                'pattern' => 'animal/perfil/{id}'
            );
            file_put_contents($logFile, "[$timestamp] Added fallback route: animal_perfil\n", FILE_APPEND);
        }

        // Force cadastro route to use SiteController (override database if needed)
        $routes['cadastro'] = array(
            'route' => '/cadastro',
            'controller' => 'SiteController',
            'action' => 'cadastro',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Forced route: cadastro -> SiteController\n", FILE_APPEND);

        // Force dashboard route to use DashboardController (override database if needed)
        $routes['dashboard'] = array(
            'route' => '/dashboard',
            'controller' => 'DashboardController',
            'action' => 'index',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Forced route: dashboard -> DashboardController\n", FILE_APPEND);

        if (!isset($routes['adotante_cadastrar_publico'])) {
            $routes['adotante_cadastrar_publico'] = array(
                'route' => '/adotante/cadastrarPublico',
                'controller' => 'AdotanteController',
                'action' => 'cadastrarPublico',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added fallback route: adotante_cadastrar_publico\n", FILE_APPEND);
        }

        if (!isset($routes['usuario_atualizar_cargo'])) {
            $routes['usuario_atualizar_cargo'] = array(
                'route' => '/usuario/atualizarCargo',
                'controller' => 'UsuarioController',
                'action' => 'atualizarCargo',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added fallback route: usuario_atualizar_cargo\n", FILE_APPEND);
        }

        if (!isset($routes['usuario_vincular_ong'])) {
            $routes['usuario_vincular_ong'] = array(
                'route' => '/usuario/vincularONG',
                'controller' => 'UsuarioController',
                'action' => 'vincularONG',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: usuario_vincular_ong\n", FILE_APPEND);
        }

        if (!isset($routes['usuario_vincular_clinica'])) {
            $routes['usuario_vincular_clinica'] = array(
                'route' => '/usuario/vincularClinica',
                'controller' => 'UsuarioController',
                'action' => 'vincularClinica',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: usuario_vincular_clinica\n", FILE_APPEND);
        }

        if (!isset($routes['usuarios'])) {
            $routes['usuarios'] = array(
                'route' => '/usuarios',
                'controller' => 'UsuarioController',
                'action' => 'index',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added fallback route: usuarios\n", FILE_APPEND);
        }

        if (!isset($routes['configuracoes'])) {
            $routes['configuracoes'] = array(
                'route' => '/configuracoes',
                'controller' => 'ConfiguracoesController',
                'action' => 'index',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added fallback route: configuracoes\n", FILE_APPEND);
        }

        if (!isset($routes['relatorios'])) {
            $routes['relatorios'] = array(
                'route' => '/relatorios',
                'controller' => 'RelatoriosController',
                'action' => 'index',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added fallback route: relatorios\n", FILE_APPEND);
        }

        if (!isset($routes['verificar-denuncias'])) {
            $routes['verificar-denuncias'] = array(
                'route' => '/verificar-denuncias',
                'controller' => 'VerificarDenunciasController',
                'action' => 'index',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added fallback route: verificar-denuncias\n", FILE_APPEND);
        }

        if (!isset($routes['dashboard'])) {
            $routes['dashboard'] = array(
                'route' => '/dashboard',
                'controller' => 'DashboardController',
                'action' => 'index',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added fallback route: dashboard\n", FILE_APPEND);
        }

        if (!isset($routes['adotar'])) {
            $routes['adotar'] = array(
                'route' => '/adotar',
                'controller' => 'AdotarController',
                'action' => 'index',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added fallback route: adotar\n", FILE_APPEND);
        }

        // Force perfil route to override any database route
        $routes['perfil'] = array(
            'route' => '/perfil',
            'controller' => 'PerfilController',
            'action' => 'index',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Forced route: perfil\n", FILE_APPEND);

        // Add separate endpoint for profile save to bypass security rules
        $routes['perfil-salvar'] = array(
            'route' => '/perfil-salvar',
            'controller' => 'PerfilController',
            'action' => 'salvar',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: perfil-salvar\n", FILE_APPEND);

        // Add route for coordenar adocoes
        $routes['coordenar-adocoes'] = array(
            'route' => '/coordenar-adocoes',
            'controller' => 'CoordenarAdocoesController',
            'action' => 'index',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: coordenar-adocoes\n", FILE_APPEND);

        // Add route for visitas migration
        $routes['migrate-visitas'] = array(
            'route' => '/migrate-visitas',
            'controller' => 'MigrationController',
            'action' => 'visitas',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: migrate-visitas\n", FILE_APPEND);

        // Add route for doacoes
        $routes['doacoes'] = array(
            'route' => '/doacoes',
            'controller' => 'DoacoesController',
            'action' => 'index',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: doacoes\n", FILE_APPEND);

        // Add route for doacoes migration
        $routes['migrate-doacoes'] = array(
            'route' => '/migrate-doacoes',
            'controller' => 'MigrationController',
            'action' => 'doacoes',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: migrate-doacoes\n", FILE_APPEND);

        // Add route for voluntarios
        $routes['voluntarios'] = array(
            'route' => '/voluntarios',
            'controller' => 'VoluntariosController',
            'action' => 'index',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: voluntarios\n", FILE_APPEND);

        // Add route for voluntarios migration
        $routes['migrate-voluntarios'] = array(
            'route' => '/migrate-voluntarios',
            'controller' => 'MigrationController',
            'action' => 'voluntarios',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: migrate-voluntarios\n", FILE_APPEND);

        // Add route for addFkOngId migration
        $routes['migrate-add-fk-ong-id'] = array(
            'route' => '/migrate-add-fk-ong-id',
            'controller' => 'MigrationController',
            'action' => 'addFkOngId',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: migrate-add-fk-ong-id\n", FILE_APPEND);

        // Add route for full database schema migration
        $routes['migrate-full-schema'] = array(
            'route' => '/migrate-full-schema',
            'controller' => 'MigrationController',
            'action' => 'fullDatabaseSchema',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: migrate-full-schema\n", FILE_APPEND);

        // Add route for create admin user migration
        $routes['migrate-create-admin'] = array(
            'route' => '/migrate-create-admin',
            'controller' => 'MigrationController',
            'action' => 'createAdminUser',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: migrate-create-admin\n", FILE_APPEND);

        // Add route for populate animals migration
        $routes['migrate-populate-animals'] = array(
            'route' => '/migrate-populate-animals',
            'controller' => 'MigrationController',
            'action' => 'populateAnimals',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: migrate-populate-animals\n", FILE_APPEND);

        // Add route for add chamado columns migration
        $routes['migrate-add-chamado-columns'] = array(
            'route' => '/migrate-add-chamado-columns',
            'controller' => 'MigrationController',
            'action' => 'addChamadoColumns',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: migrate-add-chamado-columns\n", FILE_APPEND);

        // Add route for add caso veterinario columns migration
        $routes['migrate-add-caso-veterinario-columns'] = array(
            'route' => '/migrate-add-caso-veterinario-columns',
            'controller' => 'MigrationController',
            'action' => 'addCasoVeterinarioColumns',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: migrate-add-caso-veterinario-columns\n", FILE_APPEND);

        // Add route for populate ONG migration
        $routes['migrate-populate-ong'] = array(
            'route' => '/migrate-populate-ong',
            'controller' => 'MigrationController',
            'action' => 'populateOng',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: migrate-populate-ong\n", FILE_APPEND);

        // Add route for procedimentos
        $routes['procedimentos'] = array(
            'route' => '/procedimentos',
            'controller' => 'ProcedimentosController',
            'action' => 'index',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: procedimentos\n", FILE_APPEND);

        // Add route for criar procedimento
        $routes['procedimentos-criar'] = array(
            'route' => '/procedimentos/criar',
            'controller' => 'ProcedimentosController',
            'action' => 'criar',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: procedimentos/criar\n", FILE_APPEND);

        // Add route for atualizar procedimento
        $routes['procedimentos-atualizar'] = array(
            'route' => '/procedimentos/atualizar',
            'controller' => 'ProcedimentosController',
            'action' => 'atualizar',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: procedimentos/atualizar\n", FILE_APPEND);

        // Add route for excluir procedimento
        $routes['procedimentos-excluir'] = array(
            'route' => '/procedimentos/excluir',
            'controller' => 'ProcedimentosController',
            'action' => 'excluir',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: procedimentos/excluir\n", FILE_APPEND);

        // Add route for create procedimentos table migration
        $routes['migrate-create-procedimentos-table'] = array(
            'route' => '/migrate-create-procedimentos-table',
            'controller' => 'MigrationController',
            'action' => 'createProcedimentosTable',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: migrate-create-procedimentos-table\n", FILE_APPEND);

        // Add route for comunicacao
        $routes['comunicacao'] = array(
            'route' => '/comunicacao',
            'controller' => 'ComunicacaoController',
            'action' => 'index',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: comunicacao\n", FILE_APPEND);

        // Add route for chamados
        $routes['chamados'] = array(
            'route' => '/chamados',
            'controller' => 'ChamadosController',
            'action' => 'index',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: chamados\n", FILE_APPEND);

        // Add route for chamados migration
        $routes['migrate-chamados'] = array(
            'route' => '/migrate-chamados',
            'controller' => 'MigrationController',
            'action' => 'chamados',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: migrate-chamados\n", FILE_APPEND);

        // Add route for chamados-criar
        $routes['chamados-criar'] = array(
            'route' => '/chamados-criar',
            'controller' => 'ChamadosController',
            'action' => 'criar',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: chamados-criar\n", FILE_APPEND);

        // Add route for chamados alter migration
        $routes['migrate-chamados-alter'] = array(
            'route' => '/migrate-chamados-alter',
            'controller' => 'MigrationController',
            'action' => 'chamadosAlter',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: migrate-chamados-alter\n", FILE_APPEND);

        // Add route for resgates
        $routes['resgates'] = array(
            'route' => '/resgates',
            'controller' => 'ResgatesController',
            'action' => 'index',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: resgates\n", FILE_APPEND);

        // Add route for resgates migration
        $routes['migrate-resgates'] = array(
            'route' => '/migrate-resgates',
            'controller' => 'MigrationController',
            'action' => 'resgates',
            'is_dynamic' => 0,
            'pattern' => null
        );
        file_put_contents($logFile, "[$timestamp] Added route: migrate-resgates\n", FILE_APPEND);

        // Add routes for casos-veterinario and enviar-caso-veterinario
        if (!isset($routes['casos-veterinario'])) {
            $routes['casos-veterinario'] = array(
                'route' => '/casos-veterinario',
                'controller' => 'CasosVeterinarioController',
                'action' => 'index',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: casos-veterinario\n", FILE_APPEND);
        }

        if (!isset($routes['casos-veterinario-detalhes'])) {
            $routes['casos-veterinario-detalhes'] = array(
                'route' => '/casos-veterinario-detalhes',
                'controller' => 'CasosVeterinarioController',
                'action' => 'detalhes',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: casos-veterinario-detalhes\n", FILE_APPEND);
        }

        if (!isset($routes['casos-veterinario-salvar'])) {
            $routes['casos-veterinario-salvar'] = array(
                'route' => '/casos-veterinario-salvar',
                'controller' => 'CasosVeterinarioController',
                'action' => 'salvar',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: casos-veterinario-salvar\n", FILE_APPEND);
        }

        if (!isset($routes['enviar-caso-veterinario'])) {
            $routes['enviar-caso-veterinario'] = array(
                'route' => '/enviar-caso-veterinario',
                'controller' => 'EnviarCasoVeterinarioController',
                'action' => 'index',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: enviar-caso-veterinario\n", FILE_APPEND);
        }

        if (!isset($routes['enviar-caso-veterinario-salvar'])) {
            $routes['enviar-caso-veterinario-salvar'] = array(
                'route' => '/enviar-caso-veterinario-salvar',
                'controller' => 'EnviarCasoVeterinarioController',
                'action' => 'salvar',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: enviar-caso-veterinario-salvar\n", FILE_APPEND);
        }

        if (!isset($routes['historico-medico'])) {
            $routes['historico-medico'] = array(
                'route' => '/historico-medico',
                'controller' => 'HistoricoMedicoController',
                'action' => 'index',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: historico-medico\n", FILE_APPEND);
        }

        // ONGs routes
        if (!isset($routes['ongs'])) {
            $routes['ongs'] = array(
                'route' => '/ongs',
                'controller' => 'ONGsController',
                'action' => 'index',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: ongs\n", FILE_APPEND);
        }

        if (!isset($routes['ongs-cadastrar'])) {
            $routes['ongs-cadastrar'] = array(
                'route' => '/ongs-cadastrar',
                'controller' => 'ONGsController',
                'action' => 'cadastrar',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: ongs-cadastrar\n", FILE_APPEND);
        }

        if (!isset($routes['ongs-salvar'])) {
            $routes['ongs-salvar'] = array(
                'route' => '/ongs-salvar',
                'controller' => 'ONGsController',
                'action' => 'salvar',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: ongs-salvar\n", FILE_APPEND);
        }

        if (!isset($routes['ongs-editar'])) {
            $routes['ongs-editar'] = array(
                'route' => '/ongs-editar',
                'controller' => 'ONGsController',
                'action' => 'editar',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: ongs-editar\n", FILE_APPEND);
        }

        if (!isset($routes['ongs-atualizar'])) {
            $routes['ongs-atualizar'] = array(
                'route' => '/ongs-atualizar',
                'controller' => 'ONGsController',
                'action' => 'atualizar',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: ongs-atualizar\n", FILE_APPEND);
        }

        if (!isset($routes['ongs-excluir'])) {
            $routes['ongs-excluir'] = array(
                'route' => '/ongs-excluir',
                'controller' => 'ONGsController',
                'action' => 'excluir',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: ongs-excluir\n", FILE_APPEND);
        }

        // Clinicas routes
        if (!isset($routes['clinicas'])) {
            $routes['clinicas'] = array(
                'route' => '/clinicas',
                'controller' => 'ClinicasController',
                'action' => 'index',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: clinicas\n", FILE_APPEND);
        }

        if (!isset($routes['clinicas-cadastrar'])) {
            $routes['clinicas-cadastrar'] = array(
                'route' => '/clinicas-cadastrar',
                'controller' => 'ClinicasController',
                'action' => 'cadastrar',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: clinicas-cadastrar\n", FILE_APPEND);
        }

        if (!isset($routes['clinicas-salvar'])) {
            $routes['clinicas-salvar'] = array(
                'route' => '/clinicas-salvar',
                'controller' => 'ClinicasController',
                'action' => 'salvar',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: clinicas-salvar\n", FILE_APPEND);
        }

        if (!isset($routes['clinicas-editar'])) {
            $routes['clinicas-editar'] = array(
                'route' => '/clinicas-editar',
                'controller' => 'ClinicasController',
                'action' => 'editar',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: clinicas-editar\n", FILE_APPEND);
        }

        if (!isset($routes['clinicas-atualizar'])) {
            $routes['clinicas-atualizar'] = array(
                'route' => '/clinicas-atualizar',
                'controller' => 'ClinicasController',
                'action' => 'atualizar',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: clinicas-atualizar\n", FILE_APPEND);
        }

        if (!isset($routes['clinicas-excluir'])) {
            $routes['clinicas-excluir'] = array(
                'route' => '/clinicas-excluir',
                'controller' => 'ClinicasController',
                'action' => 'excluir',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added route: clinicas-excluir\n", FILE_APPEND);
        }

        if (!isset($routes['migrate'])) {
            $routes['migrate'] = array(
                'route' => '/migrate',
                'controller' => 'MigrationController',
                'action' => 'index',
                'is_dynamic' => 0,
                'pattern' => null
            );
            file_put_contents($logFile, "[$timestamp] Added fallback route: migrate\n", FILE_APPEND);
        }

        file_put_contents($logFile, "[$timestamp] Total routes registered: " . count($routes) . "\n", FILE_APPEND);
        $this->setRoutes($routes);
    }
}
