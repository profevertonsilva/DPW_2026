<?php

namespace App\DAO;

use App\DAO;
use App\Model\AdotanteModel;
use FW\Controller\FuncoesGlobais;

class AdotanteDAO extends DAO
{
    /**
     * Mapeia colunas do banco (sem prefixo) para propriedades do Model (com prefixo adt_)
     */
    private function mapRowToModel(array $row): AdotanteModel
    {
        $model = new AdotanteModel();
        $model->__set('adt_id',          $row['id']              ?? null);
        $model->__set('adt_nome',        $row['nome']            ?? null);
        $model->__set('adt_cpf',         $row['cpf']             ?? null);
        $model->__set('adt_dn',          $row['data_nascimento'] ?? null);
        $model->__set('adt_cep',         $row['cep']             ?? null);
        $model->__set('adt_estado',      $row['estado']          ?? null);
        $model->__set('adt_cidade',      $row['cidade']          ?? null);
        $model->__set('adt_bairro',      $row['bairro']          ?? null);
        $model->__set('adt_logradouro',  $row['logradouro']      ?? null);
        $model->__set('adt_numero',      $row['numero']          ?? null);
        $model->__set('adt_complemento', $row['complemento']     ?? null);
        $model->__set('adt_tel1',        $row['telefone_1']      ?? null);
        $model->__set('adt_tel2',        $row['telefone_2']      ?? null);
        $model->__set('adt_status',      $row['status']          ?? null);
        return $model;
    }

    public function inserir($obj)
    {
        try {
            $sql = "INSERT INTO adotante (
                        nome,
                        cpf,
                        data_nascimento,
                        cep,
                        estado,
                        cidade,
                        bairro,
                        logradouro,
                        numero,
                        complemento,
                        telefone_1,
                        telefone_2,
                        status
                    ) VALUES (
                        :nome,
                        :cpf,
                        :data_nascimento,
                        :cep,
                        :estado,
                        :cidade,
                        :bairro,
                        :logradouro,
                        :numero,
                        :complemento,
                        :telefone_1,
                        :telefone_2,
                        :status
                    )";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':nome',            $obj->__get('adt_nome'));
            $stmt->bindValue(':cpf',             $obj->__get('adt_cpf'));
            $stmt->bindValue(':data_nascimento', $obj->__get('adt_dn'));
            $stmt->bindValue(':cep',             $obj->__get('adt_cep'));
            $stmt->bindValue(':estado',          $obj->__get('adt_estado'));
            $stmt->bindValue(':cidade',          $obj->__get('adt_cidade'));
            $stmt->bindValue(':bairro',          $obj->__get('adt_bairro'));
            $stmt->bindValue(':logradouro',      $obj->__get('adt_logradouro'));
            $stmt->bindValue(':numero',          $obj->__get('adt_numero'));
            $stmt->bindValue(':complemento',     $obj->__get('adt_complemento'));
            $stmt->bindValue(':telefone_1',      $obj->__get('adt_tel1'));
            $stmt->bindValue(':telefone_2',      $obj->__get('adt_tel2'));
            $stmt->bindValue(':status',          $obj->__get('adt_status'));
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
            $adotantes = array();

            $sql  = "SELECT * FROM adotante ORDER BY nome";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($result as $row) {
                array_push($adotantes, $this->mapRowToModel($row));
            }

            return $adotantes;
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function buscarPorId($id)
    {
        try {
            $sql  = "SELECT * FROM adotante WHERE id = :id";
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
            $sql = "UPDATE adotante SET
                        nome            = :nome,
                        cpf             = :cpf,
                        data_nascimento = :data_nascimento,
                        cep             = :cep,
                        estado          = :estado,
                        cidade          = :cidade,
                        bairro          = :bairro,
                        logradouro      = :logradouro,
                        numero          = :numero,
                        complemento     = :complemento,
                        telefone_1      = :telefone_1,
                        telefone_2      = :telefone_2,
                        status          = :status
                    WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id',              $obj->__get('adt_id'));
            $stmt->bindValue(':nome',            $obj->__get('adt_nome'));
            $stmt->bindValue(':cpf',             $obj->__get('adt_cpf'));
            $stmt->bindValue(':data_nascimento', $obj->__get('adt_dn'));
            $stmt->bindValue(':cep',             $obj->__get('adt_cep'));
            $stmt->bindValue(':estado',          $obj->__get('adt_estado'));
            $stmt->bindValue(':cidade',          $obj->__get('adt_cidade'));
            $stmt->bindValue(':bairro',          $obj->__get('adt_bairro'));
            $stmt->bindValue(':logradouro',      $obj->__get('adt_logradouro'));
            $stmt->bindValue(':numero',          $obj->__get('adt_numero'));
            $stmt->bindValue(':complemento',     $obj->__get('adt_complemento'));
            $stmt->bindValue(':telefone_1',      $obj->__get('adt_tel1'));
            $stmt->bindValue(':telefone_2',      $obj->__get('adt_tel2'));
            $stmt->bindValue(':status',          $obj->__get('adt_status'));
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
            $sql  = "DELETE FROM adotante WHERE id = :id";
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
