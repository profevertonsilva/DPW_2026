<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\SolicitacaoAdocaoDAO;
use App\DAO\AnimalDAO;
use App\DAO\AdotanteDAO;

class CoordenarAdocoesController extends Action
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->validaAutenticacao();

        $tipoUsuario = $_SESSION['tipo_usuario'] ?? 'adotante';

        // Mapeia o tipo_usuario do banco para os roles do sistema
        $roleMap = [
            'administrador' => 'admin',
            'ong' => 'ong',
            'veterinario' => 'vet',
            'moderador' => 'campo',
            'adotante' => 'usuario'
        ];

        $role = $roleMap[$tipoUsuario] ?? 'usuario';

        // Only admin, ong, and vet can access this page
        if (!in_array($role, ['admin', 'ong', 'vet'])) {
            header('Location: /dashboard');
            die();
        }

        $solicitacaoDAO = new SolicitacaoAdocaoDAO();
        $animalDAO = new AnimalDAO();
        $adotanteDAO = new AdotanteDAO();

        // Get approved adoptions (status = 'a' for approved)
        $adocoes = $solicitacaoDAO->listar();
        $adocoesAprovadas = array_filter($adocoes, function($adocao) {
            return $adocao->__get('solAdc_status') === 'a';
        });

        // Get additional data for each adoption
        $adocoesComDados = [];
        foreach ($adocoesAprovadas as $adocao) {
            $animal = $animalDAO->buscarPorId($adocao->__get('fk_animal_id'));
            $adotante = $adotanteDAO->buscarPorId($adocao->__get('fk_adotante_id'));
            
            $adocoesComDados[] = [
                'adocao' => $adocao,
                'animal' => $animal,
                'adotante' => $adotante
            ];
        }

        $this->render('includes/contents/coordenar_content', 'dashboard', [
            'adocoes' => $adocoesComDados
        ]);
    }

    public function validaAutenticacao()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
            header('Location: /login?erro=2');
            die();
        }

        $tipoUsuario = $_SESSION['tipo_usuario'] ?? 'adotante';

        // Mapeia o tipo_usuario do banco para os roles do sistema
        $roleMap = [
            'administrador' => 'admin',
            'ong' => 'ong',
            'veterinario' => 'vet',
            'moderador' => 'campo',
            'adotante' => 'usuario'
        ];

        $role = $roleMap[$tipoUsuario] ?? 'usuario';

        // Only admin, ong, and vet can access this page
        if (!in_array($role, ['admin', 'ong', 'vet'])) {
            header('Location: /dashboard');
            die();
        }
    }
}
