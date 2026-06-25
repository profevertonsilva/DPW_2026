<?php

use App\Controller\ApiAnimalController;
use App\Controller\ApiAuthController;
use App\Controller\ApiHealthController;
use App\Controller\ApiPublicCatalogController;
use App\Controller\ApiRankingController;
use App\Controller\ApiUserController;
use App\Controller\OngController; // Importação da sua OngController mantida aqui!
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection;

$routes->add('api.health', new Route(
    path: '/api/health',
    defaults: [
        '_controller' => ApiHealthController::class,
        '_action' => 'health',
    ],
    methods: ['GET'],
));

$routes->add('api.auth.login', new Route(
    path: '/api/auth/login',
    defaults: [
        '_controller' => ApiAuthController::class,
        '_action' => 'login',
    ],
    methods: ['POST'],
));

$routes->add('api.users.me', new Route(
    path: '/api/users/me',
    defaults: [
        '_controller' => ApiUserController::class,
        '_action' => 'me',
        '_auth' => true,
    ],
    methods: ['GET'],
));

$routes->add('api.ranking.index', new Route(
    path: '/api/ranking',
    defaults: [
        '_controller' => ApiRankingController::class,
        '_action' => 'index',
    ],
    methods: ['GET'],
));

$routes->add('api.animals.index', new Route(
    path: '/api/animals',
    defaults: [
        '_controller' => ApiAnimalController::class,
        '_action' => 'index',
    ],
    methods: ['GET'],
));

$routes->add('api.animals.show', new Route(
    path: '/api/animals/{id}',
    defaults: [
        '_controller' => ApiAnimalController::class,
        '_action' => 'show',
    ],
    requirements: [
        'id' => '\d+',
    ],
    methods: ['GET'],
));

$routes->add('api.animals.store', new Route(
    path: '/api/animals',
    defaults: [
        '_controller' => ApiAnimalController::class,
        '_action' => 'store',
        '_auth' => true,
    ],
    methods: ['POST'],
));

$routes->add('api.animals.update', new Route(
    path: '/api/animals/{id}',
    defaults: [
        '_controller' => ApiAnimalController::class,
        '_action' => 'update',
        '_auth' => true,
    ],
    requirements: [
        'id' => '\d+',
    ],
    methods: ['PUT'],
));

$routes->add('api.animals.destroy', new Route(
    path: '/api/animals/{id}',
    defaults: [
        '_controller' => ApiAnimalController::class,
        '_action' => 'destroy',
        '_auth' => true,
    ],
    requirements: [
        'id' => '\d+',
    ],
    methods: ['DELETE'],
));

/* ==========================================================================
   ROTAS DO MÓDULO DE ONGS (CORRIGIDAS PARA COMPATIBILIDADE TOTAL)
   ========================================================================== */

// PÁGINAS VISUAIS (Mantêm o prefixo dinâmico para carregar a View no Apache e 8080)
/* ==========================================================================
   ROTAS DO MÓDULO DE ONGS (ESTRATÉGIA PADRÃO E PADRONIZADA DO PROJETO)
   ========================================================================== */

$routes->add('dashboard.ong.listar', new Route(
    path: '{prefix}/dashboard/ong/listar',
    defaults: ['_controller' => OngController::class, '_action' => 'listar', 'prefix' => ''],
    requirements: ['prefix' => '.*'],
    methods: ['GET'],
));

$routes->add('dashboard.ong.cadastro', new Route(
    path: '{prefix}/dashboard/ong/cadastro',
    defaults: ['_controller' => OngController::class, '_action' => 'cadastro', 'prefix' => ''],
    requirements: ['prefix' => '.*'],
    methods: ['GET'],
));

$routes->add('dashboard.ong.editar', new Route(
    path: '{prefix}/dashboard/ong/editar',
    defaults: ['_controller' => OngController::class, '_action' => 'editar', 'prefix' => ''],
    requirements: ['prefix' => '.*'],
    methods: ['GET'],
));

$routes->add('dashboard.ong.salvar', new Route(
    path: '{prefix}/dashboard/ong/salvar',
    defaults: ['_controller' => OngController::class, '_action' => 'salvar', 'prefix' => ''],
    requirements: ['prefix' => '.*'],
    methods: ['POST'],
));

$routes->add('dashboard.ong.alterar', new Route(
    path: '{prefix}/dashboard/ong/alterar',
    defaults: ['_controller' => OngController::class, '_action' => 'alterar', 'prefix' => ''],
    requirements: ['prefix' => '.*'],
    methods: ['POST'],
));

$routes->add('dashboard.ong.excluir', new Route(
    path: '{prefix}/dashboard/ong/excluir',
    defaults: ['_controller' => OngController::class, '_action' => 'excluir', 'prefix' => ''],
    requirements: ['prefix' => '.*'],
    methods: ['GET'],
));
/* ========================================================================== */

foreach (['species', 'breeds', 'ongs', 'clinics', 'veterinarians'] as $entity) {
    $routes->add("api.{$entity}.index", new Route(
        path: "/api/{$entity}",
        defaults: [
            '_controller' => ApiPublicCatalogController::class,
            '_action' => 'index',
            '_entity' => $entity,
        ],
        methods: ['GET'],
    ));

    $routes->add("api.{$entity}.show", new Route(
        path: "/api/{$entity}/{id}",
        defaults: [
            '_controller' => ApiPublicCatalogController::class,
            '_action' => 'show',
            '_entity' => $entity,
        ],
        requirements: [
            'id' => '\d+',
        ],
        methods: ['GET'],
    ));
}

return $routes;