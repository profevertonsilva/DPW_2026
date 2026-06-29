<?php

namespace App\DAO;

use App\DAO;
use App\Model\RastreadorModel;

class RastreadorDAO extends DAO
{
    private function mapRowToModel(array $row): RastreadorModel
    {
        $model = new RastreadorModel();
        $model->__set('rast_id',          $row['id']          ?? null);
        $model->__set('rast_nome',        $row['nome']        ?? null);
        $model->__set('rast_cpf',         $row['cpf']         ?? null);
        $model->__set('rast_cep',         $row['cep']         ?? null);
        $model->__set('rast_estado',      $row['estado']      ?? null);
        $model->__set('rast_cidade',      $row['cidade']      ?? null);
        $model->__set('rast_bairro',      $row['bairro']      ?? null);
        $model->__set('rast_logradouro',  $row['logradouro']  ?? null);
        $model->__set('rast_numero',      $row['numero']      ?? null);
        $model->__set('rast_complemento', $row['complemento'] ?? null);
        $model->__set('rast_tel1',        $row['telefone']    ?? null);
        $model->__set('rast_tel2',        $row['telefone_2']  ?? null);
        return $model;
    }

    public function inserir($obj)
    {
        try {
            $sql = "INSERT INTO rastreador (
                        nome,
                        cpf,
                        cep,
                        estado,
                        cidade,
                        bairro,
                        logradouro,
                        numero,
                        complemento,
                        telefone,
                        telefone_2
                    ) VALUES (
                        :nome,
                        :cpf,
                        :cep,
                        :estado,
                        :cidade,
                        :bairro,
                        :logradouro,
                        :numero,
                        :complemento,
                        :telefone,
                        :telefone_2
                    )";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':nome',        $obj->__get('rast_nome'));
            $stmt->bindValue(':cpf',         $obj->__get('rast_cpf'));
            $stmt->bindValue(':cep',         $obj->__get('rast_cep'));
            $stmt->bindValue(':estado',      $obj->__get('rast_estado'));
            $stmt->bindValue(':cidade',      $obj->__get('rast_cidade'));
            $stmt->bindValue(':bairro',      $obj->__get('rast_bairro'));
            $stmt->bindValue(':logradouro',  $obj->__get('rast_logradouro'));
            $stmt->bindValue(':numero',      $obj->__get('rast_numero'));
            $stmt->bindValue(':complemento', $obj->__get('rast_complemento'));
            $stmt->bindValue(':telefone',    $obj->__get('rast_tel1'));
            $stmt->bindValue(':telefone_2',  $obj->__get('rast_tel2'));
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
            $sql   = "SELECT * FROM rastreador ORDER BY nome";
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
            $sql  = "SELECT * FROM rastreador WHERE id = :id";
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
            $sql = "UPDATE rastreador SET
                        nome        = :nome,
                        cpf         = :cpf,
                        cep         = :cep,
                        estado      = :estado,
                        cidade      = :cidade,
                        bairro      = :bairro,
                        logradouro  = :logradouro,
                        numero      = :numero,
                        complemento = :complemento,
                        telefone    = :telefone,
                        telefone_2  = :telefone_2
                    WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id',          $obj->__get('rast_id'));
            $stmt->bindValue(':nome',        $obj->__get('rast_nome'));
            $stmt->bindValue(':cpf',         $obj->__get('rast_cpf'));
            $stmt->bindValue(':cep',         $obj->__get('rast_cep'));
            $stmt->bindValue(':estado',      $obj->__get('rast_estado'));
            $stmt->bindValue(':cidade',      $obj->__get('rast_cidade'));
            $stmt->bindValue(':bairro',      $obj->__get('rast_bairro'));
            $stmt->bindValue(':logradouro',  $obj->__get('rast_logradouro'));
            $stmt->bindValue(':numero',      $obj->__get('rast_numero'));
            $stmt->bindValue(':complemento', $obj->__get('rast_complemento'));
            $stmt->bindValue(':telefone',    $obj->__get('rast_tel1'));
            $stmt->bindValue(':telefone_2',  $obj->__get('rast_tel2'));
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
            $sql  = "DELETE FROM rastreador WHERE id = :id";
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