<?php

namespace App\DAO;

use App\DAO;
use App\Model\OngModel;
use FW\Controller\FuncoesGlobais;

class OngDAO extends DAO {

    public function inserir($obj) {

        $ong = $obj;

        try {

            $sql = "INSERT INTO ong (
                        ong_nome,
                        ong_cnpj,
                        ong_qnt_animais,
                        ong_cep,
                        ong_estado,
                        ong_cidade,
                        ong_bairro,
                        ong_logradouro,
                        ong_numero,
                        ong_complemento,
                        ong_tel1,
                        ong_tel2,
                        ong_status
                    ) VALUES (
                        :ong_nome,
                        :ong_cnpj,
                        :ong_qnt_animais,
                        :ong_cep,
                        :ong_estado,
                        :ong_cidade,
                        :ong_bairro,
                        :ong_logradouro,
                        :ong_numero,
                        :ong_complemento,
                        :ong_tel1,
                        :ong_tel2,
                        :ong_status
                    )";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':ong_nome', $ong->__get('ong_nome'));
            $stmt->bindValue(':ong_cnpj', $ong->__get('ong_cnpj'));
            $stmt->bindValue(':ong_qnt_animais', $ong->__get('ong_qnt_animais'));
            $stmt->bindValue(':ong_cep', $ong->__get('ong_cep'));
            $stmt->bindValue(':ong_estado', $ong->__get('ong_estado'));
            $stmt->bindValue(':ong_cidade', $ong->__get('ong_cidade'));
            $stmt->bindValue(':ong_bairro', $ong->__get('ong_bairro'));
            $stmt->bindValue(':ong_logradouro', $ong->__get('ong_logradouro'));
            $stmt->bindValue(':ong_numero', $ong->__get('ong_numero'));
            $stmt->bindValue(':ong_complemento', $ong->__get('ong_complemento'));
            $stmt->bindValue(':ong_tel1', $ong->__get('ong_tel1'));
            $stmt->bindValue(':ong_tel2', $ong->__get('ong_tel2'));
            $stmt->bindValue(':ong_status', $ong->__get('ong_status'));

            return $stmt->execute();

        } catch (\Exception $e) {

            return false;
        }
    }

    public function listar() {

        try {

            $ongs = array();

            $sql = "SELECT * 
                    FROM ong
                    ORDER BY ong_nome";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->execute();

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach($result as $row) {

                $ong = new OngModel();

                $ong->__set('ong_id', $row['ong_id']);
                $ong->__set('ong_nome', $row['ong_nome']);
                $ong->__set('ong_cnpj', $row['ong_cnpj']);
                $ong->__set('ong_qnt_animais', $row['ong_qnt_animais']);
                $ong->__set('ong_cep', $row['ong_cep']);
                $ong->__set('ong_estado', $row['ong_estado']);
                $ong->__set('ong_cidade', $row['ong_cidade']);
                $ong->__set('ong_bairro', $row['ong_bairro']);
                $ong->__set('ong_logradouro', $row['ong_logradouro']);
                $ong->__set('ong_numero', $row['ong_numero']);
                $ong->__set('ong_complemento', $row['ong_complemento']);
                $ong->__set('ong_tel1', $row['ong_tel1']);
                $ong->__set('ong_tel2', $row['ong_tel2']);
                $ong->__set('ong_status', $row['ong_status']);

                $ongs[] = $ong;
            }

            return $ongs;

        } catch (\Exception $e) {

            return false;
        }
    }

    public function buscarPorId($id) {

        try {

            $sql = "SELECT * 
                    FROM ong
                    WHERE ong_id = :ong_id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':ong_id', $id);

            $stmt->execute();

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if(!$row) {
                return false;
            }

            $ong = new OngModel();

            $ong->__set('ong_id', $row['ong_id']);
            $ong->__set('ong_nome', $row['ong_nome']);
            $ong->__set('ong_cnpj', $row['ong_cnpj']);
            $ong->__set('ong_qnt_animais', $row['ong_qnt_animais']);
            $ong->__set('ong_cep', $row['ong_cep']);
            $ong->__set('ong_estado', $row['ong_estado']);
            $ong->__set('ong_cidade', $row['ong_cidade']);
            $ong->__set('ong_bairro', $row['ong_bairro']);
            $ong->__set('ong_logradouro', $row['ong_logradouro']);
            $ong->__set('ong_numero', $row['ong_numero']);
            $ong->__set('ong_complemento', $row['ong_complemento']);
            $ong->__set('ong_tel1', $row['ong_tel1']);
            $ong->__set('ong_tel2', $row['ong_tel2']);
            $ong->__set('ong_status', $row['ong_status']);

            return $ong;

        } catch (\Exception $e) {

            return false;
        }
    }

    public function alterar($obj) {

        $ong = $obj;

        try {

            $sql = "UPDATE ong SET
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

            $stmt->bindValue(':ong_id', $ong->__get('ong_id'));
            $stmt->bindValue(':ong_nome', $ong->__get('ong_nome'));
            $stmt->bindValue(':ong_cnpj', $ong->__get('ong_cnpj'));
            $stmt->bindValue(':ong_qnt_animais', $ong->__get('ong_qnt_animais'));
            $stmt->bindValue(':ong_cep', $ong->__get('ong_cep'));
            $stmt->bindValue(':ong_estado', $ong->__get('ong_estado'));
            $stmt->bindValue(':ong_cidade', $ong->__get('ong_cidade'));
            $stmt->bindValue(':ong_bairro', $ong->__get('ong_bairro'));
            $stmt->bindValue(':ong_logradouro', $ong->__get('ong_logradouro'));
            $stmt->bindValue(':ong_numero', $ong->__get('ong_numero'));
            $stmt->bindValue(':ong_complemento', $ong->__get('ong_complemento'));
            $stmt->bindValue(':ong_tel1', $ong->__get('ong_tel1'));
            $stmt->bindValue(':ong_tel2', $ong->__get('ong_tel2'));
            $stmt->bindValue(':ong_status', $ong->__get('ong_status'));

            return $stmt->execute();

        } catch (\Exception $e) {

            return false;
        }
    }

    public function excluir($id) {

        try {

            $sql = "DELETE 
                    FROM ong
                    WHERE ong_id = :ong_id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':ong_id', $id);

            return $stmt->execute();

        } catch (\Exception $e) {

            return false;
        }
    }
}