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
                        cln_cnpj,
                        cln_nome,
                        cln_cep,
                        cln_estado,
                        cln_bairro,
                        cln_logradouro,
                        cln_cidade,
                        cln_numero,
                        cln_complemento,
                        cln_tel1,
                        cln_tel2
                    ) VALUES (
                        :cln_cnpj,
                        :cln_nome,
                        :cln_cep,
                        :cln_estado,
                        :cln_bairro,
                        :cln_logradouro,
                        :cln_cidade,
                        :cln_numero,
                        :cln_complemento,
                        :cln_tel1,
                        :cln_tel2
                    )";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':cln_cnpj', $clinica->__get('cln_cnpj'));
            $stmt->bindValue(':cln_nome', $clinica->__get('cln_nome'));
            $stmt->bindValue(':cln_cep', $clinica->__get('cln_cep'));
            $stmt->bindValue(':cln_estado', $clinica->__get('cln_estado'));
            $stmt->bindValue(':cln_bairro', $clinica->__get('cln_bairro'));
            $stmt->bindValue(':cln_logradouro', $clinica->__get('cln_logradouro'));
            $stmt->bindValue(':cln_cidade', $clinica->__get('cln_cidade'));
            $stmt->bindValue(':cln_numero', $clinica->__get('cln_numero'));
            $stmt->bindValue(':cln_complemento', $clinica->__get('cln_complemento'));
            $stmt->bindValue(':cln_tel1', $clinica->__get('cln_tel1'));
            $stmt->bindValue(':cln_tel2', $clinica->__get('cln_tel2'));

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
                    ORDER BY cln_nome";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->execute();

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach($result as $row) {

                $clinica = new ClinicaModel();

                $clinica->__set('cln_id', $row['cln_id']);
                $clinica->__set('cln_cnpj', $row['cln_cnpj']);
                $clinica->__set('cln_nome', $row['cln_nome']);
                $clinica->__set('cln_cep', $row['cln_cep']);
                $clinica->__set('cln_estado', $row['cln_estado']);
                $clinica->__set('cln_bairro', $row['cln_bairro']);
                $clinica->__set('cln_logradouro', $row['cln_logradouro']);
                $clinica->__set('cln_cidade', $row['cln_cidade']);
                $clinica->__set('cln_numero', $row['cln_numero']);
                $clinica->__set('cln_complemento', $row['cln_complemento']);
                $clinica->__set('cln_tel1', $row['cln_tel1']);
                $clinica->__set('cln_tel2', $row['cln_tel2']);

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
                    WHERE cln_id = :cln_id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':cln_id', $id);

            $stmt->execute();

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if(!$row) {
                return false;
            }

            $clinica = new ClinicaModel();

            $clinica->__set('cln_id', $row['cln_id']);
            $clinica->__set('cln_cnpj', $row['cln_cnpj']);
            $clinica->__set('cln_nome', $row['cln_nome']);
            $clinica->__set('cln_cep', $row['cln_cep']);
            $clinica->__set('cln_estado', $row['cln_estado']);
            $clinica->__set('cln_bairro', $row['cln_bairro']);
            $clinica->__set('cln_logradouro', $row['cln_logradouro']);
            $clinica->__set('cln_cidade', $row['cln_cidade']);
            $clinica->__set('cln_numero', $row['cln_numero']);
            $clinica->__set('cln_complemento', $row['cln_complemento']);
            $clinica->__set('cln_tel1', $row['cln_tel1']);
            $clinica->__set('cln_tel2', $row['cln_tel2']);

            return $clinica;

        } catch (\Exception $e) {

            return false;
        }
    }

    public function alterar($obj) {

        $clinica = $obj;

        try {

            $sql = "UPDATE clinica SET
                        cln_cnpj = :cln_cnpj,
                        cln_nome = :cln_nome,
                        cln_cep = :cln_cep,
                        cln_estado = :cln_estado,
                        cln_bairro = :cln_bairro,
                        cln_logradouro = :cln_logradouro,
                        cln_cidade = :cln_cidade,
                        cln_numero = :cln_numero,
                        cln_complemento = :cln_complemento,
                        cln_tel1 = :cln_tel1,
                        cln_tel2 = :cln_tel2
                    WHERE cln_id = :cln_id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':cln_id', $clinica->__get('cln_id'));
            $stmt->bindValue(':cln_cnpj', $clinica->__get('cln_cnpj'));
            $stmt->bindValue(':cln_nome', $clinica->__get('cln_nome'));
            $stmt->bindValue(':cln_cep', $clinica->__get('cln_cep'));
            $stmt->bindValue(':cln_estado', $clinica->__get('cln_estado'));
            $stmt->bindValue(':cln_bairro', $clinica->__get('cln_bairro'));
            $stmt->bindValue(':cln_logradouro', $clinica->__get('cln_logradouro'));
            $stmt->bindValue(':cln_cidade', $clinica->__get('cln_cidade'));
            $stmt->bindValue(':cln_numero', $clinica->__get('cln_numero'));
            $stmt->bindValue(':cln_complemento', $clinica->__get('cln_complemento'));
            $stmt->bindValue(':cln_tel1', $clinica->__get('cln_tel1'));
            $stmt->bindValue(':cln_tel2', $clinica->__get('cln_tel2'));

            return $stmt->execute();

        } catch (\Exception $e) {

            return false;
        }
    }

    public function excluir($id) {

        try {

            $sql = "DELETE 
                    FROM clinica
                    WHERE cln_id = :cln_id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':cln_id', $id);

            return $stmt->execute();

        } catch (\Exception $e) {

            return false;
        }
    }
}