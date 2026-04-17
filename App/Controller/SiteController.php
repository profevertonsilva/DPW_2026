<?php

namespace App\Controller;

use FW\Controller\Action;

class SiteController extends Action {

    public function login() {
        $this->getView()->title = 'Login';
        $this->getView()->title_pagina = 'Login';

        $this->render('login', 'site');
    }

    public function dashboard() {
        $this->getView()->title = 'Dashboard';
        $this->getView()->title_pagina = 'Dashboard';

        $this->render('../dashboard/dashboard_index', 'dashboard');
    }

    public function validaAutenticacao() {
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /login');
            die();
        }
    }
}
