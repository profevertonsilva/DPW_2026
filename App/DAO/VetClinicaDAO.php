<?php

namespace App\DAO;

use App\DAO;
use App\Model\VetClinica;

class VetClinicaDAO extends DAO
{
    private function mapRowToModel(array $row): VetClinica
    {
        $model = new VetClinica();
        $model->__set('VetCli_id',         $row['id']                ?? null);
        $model->__set('fk_veterinario_id', $row['fk_veterinario_id'] ?? null);
        $model->__set('fk_clinica_id',     $row['fk_clinica_id']     ?? null);
        return $model;
    }

    public function inserir($obj)
    {
        try {
            $sql = "INSERT INTO vet_clinica (
                        fk_veterinario_id,
                        fk_clinica_id
                    ) VALUES (
                        :fk_veterinario_id,
                        :fk_clinica_id
                    )";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':fk_veterinario_id', $obj->__get('fk_veterinario_id'));
            $stmt->bindValue(':fk_clinica_id',     $obj->__get('fk_clinica_id'));
            $stmt->execute();
            return true;
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function listar()
    {
        try {
            $lista = [];
            $sql   = "SELECT * FROM vet_clinica";
            $stmt  = $this->getConn()->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                array_push($lista, $this->mapRowToModel($row));
            }
            return $lista;
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function buscarPorId($id)
    {
        try {
            $sql  = "SELECT * FROM vet_clinica WHERE id = :id";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($row) {
                return $this->mapRowToModel($row);
            }
            return false;
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function alterar($obj)
    {
        try {
            $sql = "UPDATE vet_clinica SET
                        fk_veterinario_id = :fk_veterinario_id,
                        fk_clinica_id     = :fk_clinica_id
                    WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id',                $obj->__get('VetCli_id'));
            $stmt->bindValue(':fk_veterinario_id', $obj->__get('fk_veterinario_id'));
            $stmt->bindValue(':fk_clinica_id',     $obj->__get('fk_clinica_id'));
            $stmt->execute();
            return true;
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function excluir($id)
    {
        try {
            $sql  = "DELETE FROM vet_clinica WHERE id = :id";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return true;
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
}