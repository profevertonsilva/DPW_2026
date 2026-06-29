<?php

namespace App\Controller;

use FW\Controller\Action;

class SiteController extends Action {

    public function index() {
        // Show landing page as the main index
        $this->getView()->title = 'Landing Page';
        $this->getView()->title_pagina = 'AmigoPet - Cuidado Animal';

        // Landing page is a standalone HTML file, include it directly
        include __DIR__ . '/../View/site/landing_page.php';
    }

    public function login() {
        $this->getView()->title = 'Login';
        $this->getView()->title_pagina = 'Login';

        $this->render('login', 'site');
    }

    public function dashboard() {
        $this->getView()->title = 'Dashboard';
        $this->getView()->title_pagina = 'Dashboard';

        $this->render('includes/contents/dashboard_content', 'dashboard');
    }

    public function cadastro() {
        $this->getView()->title = 'Cadastro';
        $this->getView()->title_pagina = 'Cadastro de Usuário';

        $this->render('cadastro', 'site');
    }

    public function landing() {
        $this->getView()->title = 'Landing Page';
        $this->getView()->title_pagina = 'AmigoPet - Cuidado Animal';

        // Landing page is a standalone HTML file, include it directly
        include __DIR__ . '/../View/site/landing_page.php';
    }

    public function validaAutenticacao() {
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /login');
            die();
        }
    }
}
