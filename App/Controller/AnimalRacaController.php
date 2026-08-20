<?php

namespace App\Controller;

use FW\Controller\Action;
use App\DAO\AnimalRacaDAO;
use App\DAO\AnimalDAO;
use App\DAO\RacaDAO;
use App\Model\AnimalRacaModel;

class AnimalRacaController extends Action
{
    public function vincular()
    {
        $model = new AnimalRacaModel();
        $model->__set('fk_animal_id', $_POST['fk_animal_id'] ?? null);
        $model->__set('fk_raca_id',   $_POST['fk_raca_id']   ?? null);

        $dao = new AnimalRacaDAO();
        $dao->vincular(
            (int) $model->fk_animal_id,
            (int) $model->fk_raca_id
        );

        header('Location: /dashboard/animal/editar/' . $model->fk_animal_id);
        die();
    }

    public function desvincular()
    {
        $fk_animal_id = $_POST['fk_animal_id'] ?? null;
        $fk_raca_id   = $_POST['fk_raca_id']   ?? null;

        $dao = new AnimalRacaDAO();
        $dao->desvincular((int) $fk_animal_id, (int) $fk_raca_id);

        header('Location: /dashboard/animal/editar/' . $fk_animal_id);
        die();
    }

    public function sincronizar()
    {
        $fk_animal_id = $_POST['fk_animal_id'] ?? null;
        $racaIds      = $_POST['fk_raca_id']   ?? [];

        if (!is_array($racaIds)) {
            $racaIds = [];
        }

        $dao = new AnimalRacaDAO();
        $dao->sincronizar((int) $fk_animal_id, $racaIds);

        header('Location: /dashboard/animal/editar/' . $fk_animal_id);
        die();
    }

    public function validaAutenticacao()
    {
        if (
            !isset($_SESSION['id'])   || $_SESSION['id']   == '' ||
            !isset($_SESSION['nome']) || $_SESSION['nome'] == ''
        ) {
            header('Location: /login');
            die();
        }
    }
}
