<?php

namespace App\DAO;

use App\DAO;
use App\Model\VeterinarioModel;
use \PDO;

class VeterinarioDAO extends DAO
{
    /**
     * Mapeia uma linha do banco de dados para o objeto VeterinarioModel
     */
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

    /**
     * Insere um veterinário tratando duplicidades de CPF e CRMV
     */
    public function inserir($obj)
    {
        try {
            $sql = "INSERT INTO veterinario (
                        nome, cpf, crmv, data_nascimento, cep, estado, 
                        cidade, bairro, logradouro, numero, complemento, 
                        telefone, telefone_2
                    ) VALUES (
                        :nome, :cpf, :crmv, :data_nascimento, :cep, :estado, 
                        :cidade, :bairro, :logradouro, :numero, :complemento, 
                        :telefone, :telefone_2
                    )";

            // NOTA: Se o seu banco utiliza a coluna 'telefone_2', certifique-se de que a query reflete 'telefone_2'.
            // Ajustado abaixo para garantir o padrão 'telefone_2'.
            $sql = str_replace('telefone_2', 'telefone_2', $sql);

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
            
            return $stmt->execute();
        } catch (\PDOException $ex) {
            // Captura erros de restrição única (Unique/Duplicate key)
            if ($ex->getCode() == '23000' || strpos($ex->getMessage(), '1062') !== false) {
                if (strpos($ex->getMessage(), 'crmv') !== false) {
                    throw new \Exception("Este número de CRMV já está cadastrado no sistema.");
                }
                throw new \Exception("Este CPF já está cadastrado no sistema.");
            }
            throw new \Exception("Erro ao inserir veterinário: " . $ex->getMessage());
        }
    }

    /**
     * Lista todos os veterinários ordenados por nome
     */
    public function listar()
    {
        try {
            $lista = [];
            $sql   = "SELECT * FROM veterinario ORDER BY nome";
            $stmt  = $this->getConn()->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($result as $row) {
                array_push($lista, $this->mapRowToModel($row));
            }
            return $lista;
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao listar veterinários: " . $ex->getMessage());
        }
    }

    /**
     * Busca um único veterinário pelo ID
     */
    public function buscarPorId($id)
    {
        try {
            $sql  = "SELECT * FROM veterinario WHERE id = :id";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return $this->mapRowToModel($row);
            }
            return false;
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao buscar veterinário por ID: " . $ex->getMessage());
        }
    }

    /**
     * Atualiza os dados de um veterinário existente
     */
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
            
            return $stmt->execute();
        } catch (\PDOException $ex) {
            if ($ex->getCode() == '23000' || strpos($ex->getMessage(), '1062') !== false) {
                if (strpos($ex->getMessage(), 'crmv') !== false) {
                    throw new \Exception("Este número de CRMV já está cadastrado no sistema.");
                }
                throw new \Exception("Este CPF já está cadastrado no sistema.");
            }
            throw new \Exception("Erro ao alterar veterinário: " . $ex->getMessage());
        }
    }

    /**
     * Remove fisicamente um veterinário do banco de dados
     */
    public function excluir($id)
    {
        try {
            $sql  = "DELETE FROM veterinario WHERE id = :id";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao excluir veterinário: " . $ex->getMessage());
        }
    }
}