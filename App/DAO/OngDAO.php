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
                        nome,
                        cnpj,
                        qnt_animais,
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
                        :cnpj,
                        :qnt_animais,
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

            $stmt->bindValue(':nome', $ong->__get('nome'));
            $stmt->bindValue(':cnpj', $ong->__get('cnpj'));
            $stmt->bindValue(':qnt_animais', $ong->__get('qnt_animais'));
            $stmt->bindValue(':cep', $ong->__get('cep'));
            $stmt->bindValue(':estado', $ong->__get('estado'));
            $stmt->bindValue(':cidade', $ong->__get('cidade'));
            $stmt->bindValue(':bairro', $ong->__get('bairro'));
            $stmt->bindValue(':logradouro', $ong->__get('logradouro'));
            $stmt->bindValue(':numero', $ong->__get('numero'));
            $stmt->bindValue(':complemento', $ong->__get('complemento'));
            $stmt->bindValue(':telefone_1', $ong->__get('telefone_1'));
            $stmt->bindValue(':telefone_2', $ong->__get('telefone_2'));
            $stmt->bindValue(':status', $ong->__get('status'));

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
                    ORDER BY nome";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->execute();

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach($result as $row) {

                $ong = new OngModel();

                $ong->__set('id', $row['id']);
                $ong->__set('nome', $row['nome']);
                $ong->__set('cnpj', $row['cnpj']);
                $ong->__set('qnt_animais', $row['qnt_animais']);
                $ong->__set('cep', $row['cep']);
                $ong->__set('estado', $row['estado']);
                $ong->__set('cidade', $row['cidade']);
                $ong->__set('bairro', $row['bairro']);
                $ong->__set('logradouro', $row['logradouro']);
                $ong->__set('numero', $row['numero']);
                $ong->__set('complemento', $row['complemento']);
                $ong->__set('telefone_1', $row['telefone_1']);
                $ong->__set('telefone_2', $row['telefone_2']);
                $ong->__set('status', $row['status']);

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
                    WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':id', $id);

            $stmt->execute();

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if(!$row) {
                return false;
            }

            $ong = new OngModel();

            $ong->__set('id', $row['id']);
            $ong->__set('nome', $row['nome']);
            $ong->__set('cnpj', $row['cnpj']);
            $ong->__set('qnt_animais', $row['qnt_animais']);
            $ong->__set('cep', $row['cep']);
            $ong->__set('estado', $row['estado']);
            $ong->__set('cidade', $row['cidade']);
            $ong->__set('bairro', $row['bairro']);
            $ong->__set('logradouro', $row['logradouro']);
            $ong->__set('numero', $row['numero']);
            $ong->__set('complemento', $row['complemento']);
            $ong->__set('telefone_1', $row['telefone_1']);
            $ong->__set('telefone_2', $row['telefone_2']);
            $ong->__set('status', $row['status']);

            return $ong;

        } catch (\Exception $e) {

            return false;
        }
    }

    public function alterar($obj) {

        $ong = $obj;

        try {

            $sql = "UPDATE ong SET
                        nome = :nome,
                        cnpj = :cnpj,
                        qnt_animais = :qnt_animais,
                        cep = :cep,
                        estado = :estado,
                        cidade = :cidade,
                        bairro = :bairro,
                        logradouro = :logradouro,
                        numero = :numero,
                        complemento = :complemento,
                        telefone_1 = :telefone_1,
                        telefone_2 = :telefone_2,
                        status = :status
                    WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':id', $ong->__get('id'));
            $stmt->bindValue(':nome', $ong->__get('nome'));
            $stmt->bindValue(':cnpj', $ong->__get('cnpj'));
            $stmt->bindValue(':qnt_animais', $ong->__get('qnt_animais'));
            $stmt->bindValue(':cep', $ong->__get('cep'));
            $stmt->bindValue(':estado', $ong->__get('estado'));
            $stmt->bindValue(':cidade', $ong->__get('cidade'));
            $stmt->bindValue(':bairro', $ong->__get('bairro'));
            $stmt->bindValue(':logradouro', $ong->__get('logradouro'));
            $stmt->bindValue(':numero', $ong->__get('numero'));
            $stmt->bindValue(':complemento', $ong->__get('complemento'));
            $stmt->bindValue(':telefone_1', $ong->__get('telefone_1'));
            $stmt->bindValue(':telefone_2', $ong->__get('telefone_2'));
            $stmt->bindValue(':status', $ong->__get('status'));

            return $stmt->execute();

        } catch (\Exception $e) {

            return false;
        }
    }

    public function excluir($id) {

        try {

            $sql = "DELETE 
                    FROM ong
                    WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':id', $id);

            return $stmt->execute();

        } catch (\Exception $e) {

            return false;
        }
    }
}