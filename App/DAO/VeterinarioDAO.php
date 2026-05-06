<?php

namespace App\DAO;

use App\DAO;
use App\Model\VeterinarioModel;

class VeterinarioDAO extends DAO
{
    private function mapRowToModel(array $row): VeterinarioModel
    {
        $model = new VeterinarioModel();
        $model->__set('vet_id',          $row['id']              ?? null);
        $model->__set('vet_nome',        $row['nome']            ?? null);
        $model->__set('vet_cpf',         $row['cpf']             ?? null);
        $model->__set('vet_crmv',        $row['crmv']            ?? null);
        $model->__set('vet_dn',          $row['data_nascimento'] ?? null);
        $model->__set('vet_cep',         $row['cep']             ?? null);
        $model->__set('vet_estado',      $row['estado']          ?? null);
        $model->__set('vet_cidade',      $row['cidade']          ?? null);
        $model->__set('vet_bairro',      $row['bairro']          ?? null);
        $model->__set('vet_logradouro',  $row['logradouro']      ?? null);
        $model->__set('vet_numero',      $row['numero']          ?? null);
        $model->__set('vet_complemento', $row['complemento']     ?? null);
        $model->__set('vet_tel1',        $row['telefone']        ?? null);
        $model->__set('vet_tel2',        $row['telefone_2']      ?? null);
        return $model;
    }

    public function inserir($obj)
    {
        try {
            $sql = "INSERT INTO veterinario (
                        nome,
                        cpf,
                        crmv,
                        data_nascimento,
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
                        :crmv,
                        :data_nascimento,
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
            $stmt->bindValue(':nome',            $obj->__get('vet_nome'));
            $stmt->bindValue(':cpf',             $obj->__get('vet_cpf'));
            $stmt->bindValue(':crmv',            $obj->__get('vet_crmv'));
            $stmt->bindValue(':data_nascimento', $obj->__get('vet_dn'));
            $stmt->bindValue(':cep',             $obj->__get('vet_cep'));
            $stmt->bindValue(':estado',          $obj->__get('vet_estado'));
            $stmt->bindValue(':cidade',          $obj->__get('vet_cidade'));
            $stmt->bindValue(':bairro',          $obj->__get('vet_bairro'));
            $stmt->bindValue(':logradouro',      $obj->__get('vet_logradouro'));
            $stmt->bindValue(':numero',          $obj->__get('vet_numero'));
            $stmt->bindValue(':complemento',     $obj->__get('vet_complemento'));
            $stmt->bindValue(':telefone',        $obj->__get('vet_tel1'));
            $stmt->bindValue(':telefone_2',      $obj->__get('vet_tel2'));
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
            $sql   = "SELECT * FROM veterinario ORDER BY nome";
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
            $sql  = "SELECT * FROM veterinario WHERE id = :id";
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
            $sql = "UPDATE veterinario SET
                        nome            = :nome,
                        cpf             = :cpf,
                        crmv            = :crmv,
                        data_nascimento = :data_nascimento,
                        cep             = :cep,
                        estado          = :estado,
                        cidade          = :cidade,
                        bairro          = :bairro,
                        logradouro      = :logradouro,
                        numero          = :numero,
                        complemento     = :complemento,
                        telefone        = :telefone,
                        telefone_2      = :telefone_2
                    WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id',              $obj->__get('vet_id'));
            $stmt->bindValue(':nome',            $obj->__get('vet_nome'));
            $stmt->bindValue(':cpf',             $obj->__get('vet_cpf'));
            $stmt->bindValue(':crmv',            $obj->__get('vet_crmv'));
            $stmt->bindValue(':data_nascimento', $obj->__get('vet_dn'));
            $stmt->bindValue(':cep',             $obj->__get('vet_cep'));
            $stmt->bindValue(':estado',          $obj->__get('vet_estado'));
            $stmt->bindValue(':cidade',          $obj->__get('vet_cidade'));
            $stmt->bindValue(':bairro',          $obj->__get('vet_bairro'));
            $stmt->bindValue(':logradouro',      $obj->__get('vet_logradouro'));
            $stmt->bindValue(':numero',          $obj->__get('vet_numero'));
            $stmt->bindValue(':complemento',     $obj->__get('vet_complemento'));
            $stmt->bindValue(':telefone',        $obj->__get('vet_tel1'));
            $stmt->bindValue(':telefone_2',      $obj->__get('vet_tel2'));
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
            $sql  = "DELETE FROM veterinario WHERE id = :id";
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
