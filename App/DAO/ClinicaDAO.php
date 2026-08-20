<?php

namespace App\DAO;

use App\DAO;
use App\Model\ClinicaModel;
use FW\Controller\FuncoesGlobais;

class ClinicaDAO extends DAO {

    public function inserir($obj) {

        $clinica = $obj;

        try {

            $sql = "INSERT INTO clinica (
                        cnpj,
                        nome,
                        cep,
                        estado,
                        bairro,
                        logradouro,
                        cidade,
                        numero,
                        complemento,
                        telefone_1,
                        telefone_2
                    ) VALUES (
                        :cnpj,
                        :nome,
                        :cep,
                        :estado,
                        :bairro,
                        :logradouro,
                        :cidade,
                        :numero,
                        :complemento,
                        :telefone_1,
                        :telefone_2
                    )";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':cnpj', $clinica->__get('cnpj'));
            $stmt->bindValue(':nome', $clinica->__get('nome'));
            $stmt->bindValue(':cep', $clinica->__get('cep'));
            $stmt->bindValue(':estado', $clinica->__get('estado'));
            $stmt->bindValue(':bairro', $clinica->__get('bairro'));
            $stmt->bindValue(':logradouro', $clinica->__get('logradouro'));
            $stmt->bindValue(':cidade', $clinica->__get('cidade'));
            $stmt->bindValue(':numero', $clinica->__get('numero'));
            $stmt->bindValue(':complemento', $clinica->__get('complemento'));
            $stmt->bindValue(':telefone_1', $clinica->__get('telefone_1'));
            $stmt->bindValue(':telefone_2', $clinica->__get('telefone_2'));

            return $stmt->execute();

        } catch (\Exception $e) {

            return false;
        }
    }

    public function listar() {

        try {

            $clinicas = array();

            $sql = "SELECT * 
                    FROM clinica
                    ORDER BY nome";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->execute();

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach($result as $row) {

                $clinica = new ClinicaModel();

                $clinica->__set('id', $row['id']);
                $clinica->__set('cnpj', $row['cnpj']);
                $clinica->__set('nome', $row['nome']);
                $clinica->__set('cep', $row['cep']);
                $clinica->__set('estado', $row['estado']);
                $clinica->__set('bairro', $row['bairro']);
                $clinica->__set('logradouro', $row['logradouro']);
                $clinica->__set('cidade', $row['cidade']);
                $clinica->__set('numero', $row['numero']);
                $clinica->__set('complemento', $row['complemento']);
                $clinica->__set('telefone_1', $row['telefone_1']);
                $clinica->__set('telefone_2', $row['telefone_2']);

                $clinicas[] = $clinica;
            }

            return $clinicas;

        } catch (\Exception $e) {

            return false;
        }
    }

    public function buscarPorId($id) {

        try {

            $sql = "SELECT * 
                    FROM clinica
                    WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':id', $id);

            $stmt->execute();

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if(!$row) {
                return false;
            }

            $clinica = new ClinicaModel();

            $clinica->__set('id', $row['id']);
            $clinica->__set('cnpj', $row['cnpj']);
            $clinica->__set('nome', $row['nome']);
            $clinica->__set('cep', $row['cep']);
            $clinica->__set('estado', $row['estado']);
            $clinica->__set('bairro', $row['bairro']);
            $clinica->__set('logradouro', $row['logradouro']);
            $clinica->__set('cidade', $row['cidade']);
            $clinica->__set('numero', $row['numero']);
            $clinica->__set('complemento', $row['complemento']);
            $clinica->__set('telefone_1', $row['telefone_1']);
            $clinica->__set('telefone_2', $row['telefone_2']);

            return $clinica;

        } catch (\Exception $e) {

            return false;
        }
    }

    public function alterar($obj) {

        $clinica = $obj;

        try {

            $sql = "UPDATE clinica SET
                        cnpj = :cnpj,
                        nome = :nome,
                        cep = :cep,
                        estado = :estado,
                        bairro = :bairro,
                        logradouro = :logradouro,
                        cidade = :cidade,
                        numero = :numero,
                        complemento = :complemento,
                        telefone_1 = :telefone_1,
                        telefone_2 = :telefone_2
                    WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':id', $clinica->__get('id'));
            $stmt->bindValue(':cnpj', $clinica->__get('cnpj'));
            $stmt->bindValue(':nome', $clinica->__get('nome'));
            $stmt->bindValue(':cep', $clinica->__get('cep'));
            $stmt->bindValue(':estado', $clinica->__get('estado'));
            $stmt->bindValue(':bairro', $clinica->__get('bairro'));
            $stmt->bindValue(':logradouro', $clinica->__get('logradouro'));
            $stmt->bindValue(':cidade', $clinica->__get('cidade'));
            $stmt->bindValue(':numero', $clinica->__get('numero'));
            $stmt->bindValue(':complemento', $clinica->__get('complemento'));
            $stmt->bindValue(':telefone_1', $clinica->__get('telefone_1'));
            $stmt->bindValue(':telefone_2', $clinica->__get('telefone_2'));

            return $stmt->execute();

        } catch (\Exception $e) {

            return false;
        }
    }

    public function excluir($id) {

        try {

            $sql = "DELETE 
                    FROM clinica
                    WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':id', $id);

            return $stmt->execute();

        } catch (\Exception $e) {

            return false;
        }
    }
}