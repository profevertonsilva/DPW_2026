<?php

namespace App\DAO;

use App\DAO;
use App\Model\VeterinarioModel;
use FW\Controller\FuncoesGlobais;

class VeterinarioDAO extends DAO
{
    public function inserir($obj)
    {
        try {

            $sql = "INSERT INTO veterinario (
                        vet_nome,
                        vet_cpf,
                        vet_crmv,
                        vet_dn,
                        vet_cep,
                        vet_estado,
                        vet_cidade,
                        vet_bairro,
                        vet_logradouro,
                        vet_numero,
                        vet_complemento,
                        vet_tel1,
                        vet_tel2
                    ) VALUES (
                        :vet_nome,
                        :vet_cpf,
                        :vet_crmv,
                        :vet_dn,
                        :vet_cep,
                        :vet_estado,
                        :vet_cidade,
                        :vet_bairro,
                        :vet_logradouro,
                        :vet_numero,
                        :vet_complemento,
                        :vet_tel1,
                        :vet_tel2
                    )";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':vet_nome', $obj->__get('vet_nome'));
            $stmt->bindValue(':vet_cpf', $obj->__get('vet_cpf'));
            $stmt->bindValue(':vet_crmv', $obj->__get('vet_crmv'));
            $stmt->bindValue(':vet_dn', $obj->__get('vet_dn'));
            $stmt->bindValue(':vet_cep', $obj->__get('vet_cep'));
            $stmt->bindValue(':vet_estado', $obj->__get('vet_estado'));
            $stmt->bindValue(':vet_cidade', $obj->__get('vet_cidade'));
            $stmt->bindValue(':vet_bairro', $obj->__get('vet_bairro'));
            $stmt->bindValue(':vet_logradouro', $obj->__get('vet_logradouro'));
            $stmt->bindValue(':vet_numero', $obj->__get('vet_numero'));
            $stmt->bindValue(':vet_complemento', $obj->__get('vet_complemento'));
            $stmt->bindValue(':vet_tel1', $obj->__get('vet_tel1'));
            $stmt->bindValue(':vet_tel2', $obj->__get('vet_tel2'));

            $stmt->execute();

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function listar()
    {
        try {

            $veterinarios = [];

            $sql = "SELECT * FROM veterinario";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($result as $row) {

                $veterinario = new VeterinarioModel();

                $global = new FuncoesGlobais();
                $global->popularModel($veterinario, $row);

                array_push($veterinarios, $veterinario);
            }

            return $veterinarios;

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function buscarPorId($id)
    {
        try {

            $sql = "SELECT * 
                    FROM veterinario 
                    WHERE vet_id = :id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(
                ':id',
                $id,
                \PDO::PARAM_INT
            );

            $stmt->execute();

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($result) {

                $veterinario = new VeterinarioModel();

                $global = new FuncoesGlobais();
                $global->popularModel(
                    $veterinario,
                    $result
                );

                return $veterinario;
            }

            return null;

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function alterar($obj)
    {
        try {

            $sql = "UPDATE veterinario SET

                        vet_nome = :vet_nome,
                        vet_cpf = :vet_cpf,
                        vet_crmv = :vet_crmv,
                        vet_dn = :vet_dn,
                        vet_cep = :vet_cep,
                        vet_estado = :vet_estado,
                        vet_cidade = :vet_cidade,
                        vet_bairro = :vet_bairro,
                        vet_logradouro = :vet_logradouro,
                        vet_numero = :vet_numero,
                        vet_complemento = :vet_complemento,
                        vet_tel1 = :vet_tel1,
                        vet_tel2 = :vet_tel2

                    WHERE
                        vet_id = :vet_id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(':vet_id', $obj->__get('vet_id'));
            $stmt->bindValue(':vet_nome', $obj->__get('vet_nome'));
            $stmt->bindValue(':vet_cpf', $obj->__get('vet_cpf'));
            $stmt->bindValue(':vet_crmv', $obj->__get('vet_crmv'));
            $stmt->bindValue(':vet_dn', $obj->__get('vet_dn'));
            $stmt->bindValue(':vet_cep', $obj->__get('vet_cep'));
            $stmt->bindValue(':vet_estado', $obj->__get('vet_estado'));
            $stmt->bindValue(':vet_cidade', $obj->__get('vet_cidade'));
            $stmt->bindValue(':vet_bairro', $obj->__get('vet_bairro'));
            $stmt->bindValue(':vet_logradouro', $obj->__get('vet_logradouro'));
            $stmt->bindValue(':vet_numero', $obj->__get('vet_numero'));
            $stmt->bindValue(':vet_complemento', $obj->__get('vet_complemento'));
            $stmt->bindValue(':vet_tel1', $obj->__get('vet_tel1'));
            $stmt->bindValue(':vet_tel2', $obj->__get('vet_tel2'));

            $stmt->execute();

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function excluir($obj)
    {
        try {

            $sql = "DELETE 
                    FROM veterinario 
                    WHERE vet_id = :id";

            $stmt = $this->getConn()->prepare($sql);

            $stmt->bindValue(
                ':id',
                $obj->__get('vet_id'),
                \PDO::PARAM_INT
            );

            $stmt->execute();

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
}