<?php

namespace App\DAO;

use App\DAO;
use App\Model\OngModel;
use FW\Controller\FuncoesGlobais;
use \PDO;

class OngDAO extends DAO
{
    /**
     * Insere uma nova ONG com tratamento de duplicidade de CNPJ
     */
    public function inserir($obj)
    {
        try {
            $sql = "INSERT INTO ong (
                        ong_nome, ong_cnpj, ong_qnt_animais, ong_cep, ong_estado, 
                        ong_cidade, ong_bairro, ong_logradouro, ong_numero, 
                        ong_complemento, ong_tel1, ong_tel2, ong_status
                    ) VALUES (
                        :ong_nome, :ong_cnpj, :ong_qnt_animais, :ong_cep, :ong_estado, 
                        :ong_cidade, :ong_bairro, :ong_logradouro, :ong_numero, 
                        :ong_complemento, :ong_tel1, :ong_tel2, :ong_status
                    )";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':ong_nome', $obj->__get('ong_nome'));
            $stmt->bindValue(':ong_cnpj', $obj->__get('ong_cnpj'));
            $stmt->bindValue(':ong_qnt_animais', $obj->__get('ong_qnt_animais') ?? 0);
            $stmt->bindValue(':ong_cep', $obj->__get('ong_cep'));
            $stmt->bindValue(':ong_estado', $obj->__get('ong_estado'));
            $stmt->bindValue(':ong_cidade', $obj->__get('ong_cidade'));
            $stmt->bindValue(':ong_bairro', $obj->__get('ong_bairro'));
            $stmt->bindValue(':ong_logradouro', $obj->__get('ong_logradouro'));
            $stmt->bindValue(':ong_numero', $obj->__get('ong_numero'));
            $stmt->bindValue(':ong_complemento', $obj->__get('ong_complemento'));
            $stmt->bindValue(':ong_tel1', $obj->__get('ong_tel1'));
            $stmt->bindValue(':ong_tel2', $obj->__get('ong_tel2'));
            $stmt->bindValue(':ong_status', $obj->__get('ong_status') ?? 1);

            return $stmt->execute();
        } catch (\PDOException $e) {
            // Captura o código de erro 23000 (violação de restrição) ou 1062 (Duplicate entry do MySQL)
            if ($e->getCode() == '23000' || strpos($e->getMessage(), '1062') !== false) {
                throw new \Exception("Este CNPJ já está cadastrado no sistema.");
            }
            
            throw new \Exception("Erro ao inserir ONG: " . $e->getMessage());
        }
    }

    /**
     * Atualiza os dados de uma ONG existente
     */
    public function alterar($obj)
    {
        try {
            $sql = "UPDATE ong 
                    SET 
                        ong_nome = :ong_nome,
                        ong_cnpj = :ong_cnpj,
                        ong_qnt_animais = :ong_qnt_animais,
                        ong_cep = :ong_cep,
                        ong_estado = :ong_estado,
                        ong_cidade = :ong_cidade,
                        ong_bairro = :ong_bairro,
                        ong_logradouro = :ong_logradouro,
                        ong_numero = :ong_numero,
                        ong_complemento = :ong_complemento,
                        ong_tel1 = :ong_tel1,
                        ong_tel2 = :ong_tel2,
                        ong_status = :ong_status
                    WHERE ong_id = :ong_id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':ong_id', $obj->__get('ong_id'));
            $stmt->bindValue(':ong_nome', $obj->__get('ong_nome'));
            $stmt->bindValue(':ong_cnpj', $obj->__get('ong_cnpj'));
            $stmt->bindValue(':ong_qnt_animais', $obj->__get('ong_qnt_animais') ?? 0);
            $stmt->bindValue(':ong_cep', $obj->__get('ong_cep'));
            $stmt->bindValue(':ong_estado', $obj->__get('ong_estado'));
            $stmt->bindValue(':ong_cidade', $obj->__get('ong_cidade'));
            $stmt->bindValue(':ong_bairro', $obj->__get('ong_bairro'));
            $stmt->bindValue(':ong_logradouro', $obj->__get('ong_logradouro'));
            $stmt->bindValue(':ong_numero', $obj->__get('ong_numero'));
            $stmt->bindValue(':ong_complemento', $obj->__get('ong_complemento'));
            $stmt->bindValue(':ong_tel1', $obj->__get('ong_tel1'));
            $stmt->bindValue(':ong_tel2', $obj->__get('ong_tel2'));
            $stmt->bindValue(':ong_status', $obj->__get('ong_status') ?? 1);

            return $stmt->execute();
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao alterar ONG: " . $ex->getMessage());
        }
    }

    /**
     * Remove uma ONG permanentemente
     */
    public function excluir($id)
    {
        try {
            $sql = "DELETE FROM ong WHERE ong_id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(":id", $id);
            return $stmt->execute();
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao excluir ONG: " . $ex->getMessage());
        }
    }

    /**
     * Busca uma ONG específica pelo ID para edição
     */
    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT * FROM ong WHERE ong_id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado && count($resultado) > 0) {
                $ongModel = new OngModel();

                $global = new FuncoesGlobais();
                $global->popularModel($ongModel, $resultado);

                return $ongModel;
            }

            return false;
        } catch (\PDOException $ex) {
            throw new \Exception("Erro ao buscar ONG por ID: " . $ex->getMessage());
        }
    }

    /**
     * Lista todas as ONGs cadastradas
     */
    public function listar()
    {
        try {
            $ongs = array();

            $sql = "SELECT * FROM ong ORDER BY ong_id DESC";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($resultado as $row) {
                $ongModel = new OngModel();

                $global = new FuncoesGlobais();
                $global->popularModel($ongModel, $row);

                array_push($ongs, $ongModel);
            }

            return $ongs;
        } catch (\PDOException $ex) {
            echo "<h3>Erro SQL na Listagem de ONGs:</h3><pre>" . $ex->getMessage() . "</pre>";
            die();
        }
    }
}