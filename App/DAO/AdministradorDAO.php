<?php

namespace App\DAO;

use App\DAO;
use App\Model\AdministradorModel;

class AdministradorDAO extends DAO
{
    private function mapRowToModel(array $row): AdministradorModel
    {
        $model = new AdministradorModel();
        $model->__set('adm_id',          $row['id']              ?? null);
        $model->__set('adm_nome',        $row['nome']            ?? null);
        $model->__set('adm_cpf',         $row['cpf']             ?? null);
        $model->__set('adm_cep',         $row['cep']             ?? null);
        $model->__set('adm_logradouro',  $row['logradouro']      ?? null);
        $model->__set('adm_estado',      $row['estado']          ?? null);
        $model->__set('adm_complemento', $row['complemento']     ?? null);
        $model->__set('adm_dn',          $row['data_nascimento'] ?? null);
        $model->__set('adm_cidade',      $row['cidade']          ?? null);
        $model->__set('adm_bairro',      $row['bairro']          ?? null);
        $model->__set('adm_numero',      $row['numero']          ?? null);
        $model->__set('adm_tel1',        $row['telefone']        ?? null);
        $model->__set('adm_tel2',        $row['telefone_2']      ?? null);
        return $model;
    }

    public function inserir($obj)
    {
        try {
            $sql = "INSERT INTO administrador (
                        nome,
                        cpf,
                        cep,
                        logradouro,
                        estado,
                        complemento,
                        data_nascimento,
                        cidade,
                        bairro,
                        numero,
                        telefone,
                        telefone_2
                    ) VALUES (
                        :nome,
                        :cpf,
                        :cep,
                        :logradouro,
                        :estado,
                        :complemento,
                        :data_nascimento,
                        :cidade,
                        :bairro,
                        :numero,
                        :telefone,
                        :telefone_2
                    )";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':nome',            $obj->__get('adm_nome'));
            $stmt->bindValue(':cpf',             $obj->__get('adm_cpf'));
            $stmt->bindValue(':cep',             $obj->__get('adm_cep'));
            $stmt->bindValue(':logradouro',      $obj->__get('adm_logradouro'));
            $stmt->bindValue(':estado',          $obj->__get('adm_estado'));
            $stmt->bindValue(':complemento',     $obj->__get('adm_complemento'));
            $stmt->bindValue(':data_nascimento', $obj->__get('adm_dn'));
            $stmt->bindValue(':cidade',          $obj->__get('adm_cidade'));
            $stmt->bindValue(':bairro',          $obj->__get('adm_bairro'));
            $stmt->bindValue(':numero',          $obj->__get('adm_numero'));
            $stmt->bindValue(':telefone',        $obj->__get('adm_tel1'));
            $stmt->bindValue(':telefone_2',      $obj->__get('adm_tel2'));
            $stmt->execute();
            return $this->getConn()->lastInsertId();
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function listar()
    {
        try {
            $lista = [];
            $sql   = "SELECT * FROM administrador ORDER BY nome";
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
            $sql  = "SELECT * FROM administrador WHERE id = :id";
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
            $sql = "UPDATE administrador SET
                        nome            = :nome,
                        cpf             = :cpf,
                        cep             = :cep,
                        logradouro      = :logradouro,
                        estado          = :estado,
                        complemento     = :complemento,
                        data_nascimento = :data_nascimento,
                        cidade          = :cidade,
                        bairro          = :bairro,
                        numero          = :numero,
                        telefone        = :telefone,
                        telefone_2      = :telefone_2
                    WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id',              $obj->__get('adm_id'));
            $stmt->bindValue(':nome',            $obj->__get('adm_nome'));
            $stmt->bindValue(':cpf',             $obj->__get('adm_cpf'));
            $stmt->bindValue(':cep',             $obj->__get('adm_cep'));
            $stmt->bindValue(':logradouro',      $obj->__get('adm_logradouro'));
            $stmt->bindValue(':estado',          $obj->__get('adm_estado'));
            $stmt->bindValue(':complemento',     $obj->__get('adm_complemento'));
            $stmt->bindValue(':data_nascimento', $obj->__get('adm_dn'));
            $stmt->bindValue(':cidade',          $obj->__get('adm_cidade'));
            $stmt->bindValue(':bairro',          $obj->__get('adm_bairro'));
            $stmt->bindValue(':numero',          $obj->__get('adm_numero'));
            $stmt->bindValue(':telefone',        $obj->__get('adm_tel1'));
            $stmt->bindValue(':telefone_2',      $obj->__get('adm_tel2'));
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
            $sql  = "DELETE FROM administrador WHERE id = :id";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return true;
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function listarUsuariosParaPromover()
    {
        try {
            $lista = [];
            $sql   = "SELECT id, email, tipo_usuario, status FROM login WHERE tipo_usuario != 'administrador' ORDER BY email";
            $stmt  = $this->getConn()->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function promoverUsuario($loginId)
    {
        try {
            $sql = "UPDATE login SET tipo_usuario = 'administrador' WHERE id = :id";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $loginId);
            $stmt->execute();
            return true;
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
}