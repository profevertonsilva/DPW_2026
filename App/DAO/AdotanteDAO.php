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
    // private function mapRowToModel(array $row): AdotanteModel
    // {
    //     try {
    //         $adt_nome = $obj->__get("adt_nome");
    //         $adt_cpf = $obj->__get("adt_cpf");
    //         $adt_dn = $obj->__get("adt_dn");
    //         $adt_cep = $obj->__get("adt_cep");
    //         $adt_estado = $obj->__get("adt_estado");
    //         $adt_cidade = $obj->__get("adt_cidade");
    //         $adt_bairro = $obj->__get("adt_bairro");
    //         $adt_logradouro = $obj->__get("adt_logradouro");
    //         $adt_numero = $obj->__get("adt_numero");
    //         $adt_complemento = $obj->__get("adt_complemento");
    //         $adt_tel1 = $obj->__get("adt_tel1");
    //         $adt_tel2 = $obj->__get("adt_tel2");
    //         $adt_status = $obj->__get("adt_status");


    //         $sql = "INSERT INTO adotante (
    //             nome
    //             cpf
    //             dn
    //             cep
    //             estado
    //             cidade
    //             bairro
    //             logradouro
    //             numero
    //             complemento
    //             tel1
    //             tel2
    //             status
    //         ) VALUES (
    //             :adt_nome
    //             :adt_cpf
    //             :adt_dn
    //             :adt_cep
    //             :adt_estado
    //             :adt_cidade
    //             :adt_bairro
    //             :adt_logradouro
    //             :adt_numero
    //             :adt_complemento
    //             :adt_tel1
    //             :adt_tel2
    //             :adt_status
    //         )";

    //         $stmt = $this->getConn()->prepare($sql);
    //         $stmt->bindValue('adt_nome', $adt_nome);
    //         $stmt->bindValue('adt_cpf', $adt_cpf);
    //         $stmt->bindValue('adt_dn', $adt_dn);
    //         $stmt->bindValue('adt_cep', $adt_cep);
    //         $stmt->bindValue('adt_estado', $adt_estado);
    //         $stmt->bindValue('adt_cidade', $adt_cidade);
    //         $stmt->bindValue('adt_bairro', $adt_bairro);
    //         $stmt->bindValue('adt_logradouro', $adt_logradouro);
    //         $stmt->bindValue('adt_numero', $adt_numero);
    //         $stmt->bindValue('adt_complemento', $adt_complemento);
    //         $stmt->bindValue('adt_tel1', $adt_tel1);
    //         $stmt->bindValue('adt_tel2', $adt_tel2);
    //         $stmt->bindValue('adt_status', $adt_status);
    //         $stmt->execute();
    //     } catch (\PDOException $ex) {
    //         header('Location:/error103');
    //         die();
    //     }
    // }

    public function inserir($obj)
    {
        try {
            $adt_nome = $obj->__get("adt_nome");
            $adt_cpf = $obj->__get("adt_cpf");
            $adt_dn = $obj->__get("adt_dn");
            $adt_cep = $obj->__get("adt_cep");
            $adt_estado = $obj->__get("adt_estado");
            $adt_cidade = $obj->__get("adt_cidade");
            $adt_bairro = $obj->__get("adt_bairro");
            $adt_logradouro = $obj->__get("adt_logradouro");
            $adt_numero = $obj->__get("adt_numero");
            $adt_complemento = $obj->__get("adt_complemento");
            $adt_tel1 = $obj->__get("adt_tel1");
            $adt_tel2 = $obj->__get("adt_tel2");
            $adt_status = $obj->__get("adt_status");


            $sql = "INSERT INTO adotante (
                nome,
                cpf,
                dn,
                cep,
                estado,
                cidade,
                bairro,
                logradouro,
                numero,
                complemento,
                tel1,
                tel2,
                status
            ) VALUES (
                :adt_nome,
                :adt_cpf,
                :adt_dn,
                :adt_cep,
                :adt_estado,
                :adt_cidade,
                :adt_bairro,
                :adt_logradouro,
                :adt_numero,
                :adt_complemento,
                :adt_tel1,
                :adt_tel2,
                :adt_status
            )";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue('adt_nome', $adt_nome);
            $stmt->bindValue('adt_cpf', $adt_cpf);
            $stmt->bindValue('adt_dn', $adt_dn);
            $stmt->bindValue('adt_cep', $adt_cep);
            $stmt->bindValue('adt_estado', $adt_estado);
            $stmt->bindValue('adt_cidade', $adt_cidade);
            $stmt->bindValue('adt_bairro', $adt_bairro);
            $stmt->bindValue('adt_logradouro', $adt_logradouro);
            $stmt->bindValue('adt_numero', $adt_numero);
            $stmt->bindValue('adt_complemento', $adt_complemento);
            $stmt->bindValue('adt_tel1', $adt_tel1);
            $stmt->bindValue('adt_tel2', $adt_tel2);
            $stmt->bindValue('adt_status', $adt_status);
            $stmt->execute();
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public  function excluir($id)
    {
        $sql = "DELETE FROM adotante WHERE id = :adt_id";

        $stmt = $this->getConn()->prepare($sql);
        $stmt->bindValue(":adt_id", $id);
        $stmt->execute();
    }
    public  function alterar($obj)
    {
        try {
            $adt_id = $obj->__get("adt_id");
            $adt_nome = $obj->__get("adt_nome");
            $adt_cpf = $obj->__get("adt_cpf");
            $adt_dn = $obj->__get("adt_dn");
            $adt_cep = $obj->__get("adt_cep");
            $adt_estado = $obj->__get("adt_estado");
            $adt_cidade = $obj->__get("adt_cidade");
            $adt_bairro = $obj->__get("adt_bairro");
            $adt_logradouro = $obj->__get("adt_logradouro");
            $adt_numero = $obj->__get("adt_numero");
            $adt_complemento = $obj->__get("adt_complemento");
            $adt_tel1 = $obj->__get("adt_tel1");
            $adt_tel2 = $obj->__get("adt_tel2");
            $adt_status = $obj->__get("adt_status");

            $sql = "UPDATE adotante as a
                SET 
                nome = :adt_nome,
                cpf = :adt_cpf,
                data_nascimento = :adt_dn,
                cep = :adt_cep,
                estado = :adt_estado,
                cidade = :adt_cidade,
                bairro = :adt_bairro,
                logradouro = :adt_logradouro,
                numero = :adt_numero,
                complemento = :adt_complemento,
                tel1 = :adt_tel1,
                tel2 = :adt_tel2,
                `status` = :adt_status
            WHERE id = :adt_id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue('adt_id', $adt_id);
            $stmt->bindValue('adt_nome', $adt_nome);
            $stmt->bindValue('adt_cpf', $adt_cpf);
            $stmt->bindValue('adt_dn', $adt_dn);
            $stmt->bindValue('adt_cep', $adt_cep);
            $stmt->bindValue('adt_estado', $adt_estado);
            $stmt->bindValue('adt_cidade', $adt_cidade);
            $stmt->bindValue('adt_bairro', $adt_bairro);
            $stmt->bindValue('adt_logradouro', $adt_logradouro);
            $stmt->bindValue('adt_numero', $adt_numero);
            $stmt->bindValue('adt_complemento', $adt_complemento);
            $stmt->bindValue('adt_tel1', $adt_tel1);
            $stmt->bindValue('adt_tel2', $adt_tel2);
            $stmt->bindValue('adt_status', $adt_status);
            $stmt->execute();
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
    public  function buscarPorId($id)
    {
        try {
            $sql = "SELECT * 
            FROM Adotante
            WHERE id = :adt_id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue('adt_id', $id);
            $stmt->execute();
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($resultado > 0) {
                $AdotanteModel = new AdotanteModel();
                $AdotanteModel->__set('adt_id', $resultado['id']);
                $AdotanteModel->__set('adt_nome', $resultado['nome']);
                $AdotanteModel->__set('adt_cpf', $resultado['cpf']);
                $AdotanteModel->__set('adt_dn', $resultado['data_nascimento']);
                $AdotanteModel->__set('adt_cep', $resultado['cep']);
                $AdotanteModel->__set('adt_estado', $resultado['numero']);
                $AdotanteModel->__set('adt_cidade', $resultado['cidade']);
                $AdotanteModel->__set('adt_bairro', $resultado['bairro']);
                $AdotanteModel->__set('adt_logradouro', $resultado['logradouro']);
                $AdotanteModel->__set('adt_numero', $resultado['numero']);
                $AdotanteModel->__set('adt_complemento', $resultado['complemento']);
                $AdotanteModel->__set('adt_tel1', $resultado['telefone_1']);
                $AdotanteModel->__set('adt_tel2', $resultado['telefone_2']);
                $AdotanteModel->__set('adt_status', $resultado['status']);
                return $AdotanteModel;
            }

            return false;
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
    public function listar() {
        try {
            $adotantes = array();

            $sql = "SELECT 
                a.*,
                l.email
            FROM 
                adotante a,
                login l
            WHERE
                a.fk_login_id = l.id
            ";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach($resultado as $row) {
                $adotanteModel = new AdotanteModel();

                $global = new FuncoesGlobais();
                $global->popularModel($adotanteModel, $row);

                array_push($adotantes, $adotanteModel);
            }

            return $adotantes;

        }catch(\PDOException $ex) {
            header('Location:/error103');
            die();
        }   
    }
}
