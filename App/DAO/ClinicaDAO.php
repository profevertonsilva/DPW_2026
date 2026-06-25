<?php

namespace App\DAO;

use App\DAO;
use App\Model\ClinicaModel;
use FW\Controller\FuncoesGlobais;
use \PDO;

class ClinicaDAO extends DAO
{
    /**
     * Insere uma nova clínica no banco de dados com tratamento contra CNPJ duplicado
     */
    public function inserir($obj)
    {
        try {
            $sql = "INSERT INTO clinica (
                        nome, cnpj, cep, telefone_1, logradouro, numero, 
                        bairro, cidade, estado, complemento, telefone_2
                    ) VALUES (
                        :cln_nome, :cln_cnpj, :cln_cep, :cln_tel1, :cln_logradouro, :cln_numero, 
                        :cln_bairro, :cln_cidade, :cln_estado, :cln_complemento, :cln_tel2
                    )";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':cln_nome', $obj->__get('cln_nome'));
            $stmt->bindValue(':cln_cnpj', $obj->__get('cln_cnpj'));
            $stmt->bindValue(':cln_cep', $obj->__get('cln_cep'));
            $stmt->bindValue(':cln_tel1', $obj->__get('cln_tel1'));
            $stmt->bindValue(':cln_logradouro', $obj->__get('cln_logradouro'));
            $stmt->bindValue(':cln_numero', $obj->__get('cln_numero'));
            $stmt->bindValue(':cln_bairro', $obj->__get('cln_bairro'));
            $stmt->bindValue(':cln_cidade', $obj->__get('cln_cidade'));
            $stmt->bindValue(':cln_estado', $obj->__get('cln_estado'));
            $stmt->bindValue(':cln_complemento', $obj->__get('cln_complemento'));
            $stmt->bindValue(':cln_tel2', $obj->__get('cln_tel2'));

            return $stmt->execute();
        } catch (\PDOException $ex) {
            // Captura erro de violação de chave única / duplicidade (Duplicate entry)
            if ($ex->getCode() == '23000' || strpos($ex->getMessage(), '1062') !== false) {
                throw new \Exception("Este CNPJ já está cadastrado no sistema.");
            }
            
            throw new \Exception("Erro ao inserir clínica: " . $ex->getMessage());
        }
    }

    /**
     * Atualiza os dados de uma clínica existente
     */
    public function alterar($obj)
    {
        try {
            $sql = "UPDATE clinica 
                    SET 
                        nome = :cln_nome,
                        cnpj = :cln_cnpj,
                        cep = :cln_cep,
                        telefone_1 = :cln_tel1,
                        logradouro = :cln_logradouro,
                        numero = :cln_numero,
                        bairro = :cln_bairro,
                        cidade = :cln_cidade,
                        estado = :cln_estado,
                        complemento = :cln_complemento,
                        telefone_2 = :cln_tel2
                    WHERE id = :cln_id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':cln_id', $obj->__get('cln_id'));
            $stmt->bindValue(':cln_nome', $obj->__get('cln_nome'));
            $stmt->bindValue(':cln_cnpj', $obj->__get('cln_cnpj'));
            $stmt->bindValue(':cln_cep', $obj->__get('cln_cep'));
            $stmt->bindValue(':cln_tel1', $obj->__get('cln_tel1'));
            $stmt->bindValue(':cln_logradouro', $obj->__get('cln_logradouro'));
            $stmt->bindValue(':cln_numero', $obj->__get('cln_numero'));
            $stmt->bindValue(':cln_bairro', $obj->__get('cln_bairro'));
            $stmt->bindValue(':cln_cidade', $obj->__get('cln_cidade'));
            $stmt->bindValue(':cln_estado', $obj->__get('cln_estado'));
            $stmt->bindValue(':cln_complemento', $obj->__get('cln_complemento'));
            $stmt->bindValue(':cln_tel2', $obj->__get('cln_tel2'));

            return $stmt->execute();
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao alterar clínica: " . $ex->getMessage());
        }
    }

    /**
     * Remove uma clínica permanentemente
     */
    public function excluir($id)
    {
        try {
            $sql = "DELETE FROM clinica WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(":id", $id);
            return $stmt->execute();
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao excluir clínica: " . $ex->getMessage());
        }
    }

    /**
     * Busca uma clínica específica pelo ID para edição (Atribuição Direta)
     */
    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT * FROM clinica WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $clinica = new ClinicaModel();
                $clinica->__set('cln_id', $row['id']);
                $clinica->__set('cln_nome', $row['nome']);
                $clinica->__set('cln_cnpj', $row['cnpj']);
                $clinica->__set('cln_cep', $row['cep']);
                $clinica->__set('cln_tel1', $row['telefone_1']);
                $clinica->__set('cln_logradouro', $row['logradouro']);
                $clinica->__set('cln_numero', $row['numero']);
                $clinica->__set('cln_bairro', $row['bairro']);
                $clinica->__set('cln_cidade', $row['cidade']);
                $clinica->__set('cln_estado', $row['estado']);
                $clinica->__set('cln_complemento', $row['complemento']);
                $clinica->__set('cln_tel2', $row['telefone_2']);

                return $clinica;
            }

            return false;
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao buscar clínica por ID: " . $ex->getMessage());
        }
    }

    /**
     * Lista todas as clínicas cadastradas
     */
    public function listar()
    {
        try {
            $clinicas = array();

            $sql = "SELECT 
                        id AS cln_id, nome AS cln_nome, cnpj AS cln_cnpj, cep AS cln_cep, 
                        telefone_1 AS cln_tel1, logradouro AS cln_logradouro, numero AS cln_numero, 
                        bairro AS cln_bairro, cidade AS cln_cidade, estado AS cln_estado, 
                        complemento AS cln_complemento, telefone_2 AS cln_tel2 
                    FROM clinica ORDER BY id DESC";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($resultado as $row) {
                $clinicaModel = new ClinicaModel();

                $global = new FuncoesGlobais();
                $global->popularModel($clinicaModel, $row);

                array_push($clinicas, $clinicaModel);
            }

            return $clinicas;
        } catch (\PDOException $ex) {
            echo "<h3>Erro SQL na Listagem de Clínicas:</h3><pre>" . $ex->getMessage() . "</pre>";
            die();
        }
    }
}