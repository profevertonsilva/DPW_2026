<?php

namespace App\DAO;

use App\DAO;
use App\Model\AdotanteModel;
use FW\Controller\FuncoesGlobais;

class AdotanteDAO extends DAO
{
    /**
     * Mapeia colunas do banco (sem prefixo) para propriedades do Model (com prefixo )
     */
    // private function mapRowToModel(array $row): AdotanteModel
    // {
    //     try {
    //         $nome = $obj->__get("nome");
    //         $cpf = $obj->__get("cpf");
    //         $data_nascimento = $obj->__get("data_nascimento");
    //         $cep = $obj->__get("cep");
    //         $estado = $obj->__get("estado");
    //         $cidade = $obj->__get("cidade");
    //         $bairro = $obj->__get("bairro");
    //         $logradouro = $obj->__get("logradouro");
    //         $numero = $obj->__get("numero");
    //         $complemento = $obj->__get("complemento");
    //         $telefone_1 = $obj->__get("telefone_1");
    //         $telefone_2 = $obj->__get("telefone_2");
    //         $status = $obj->__get("status");


    //         $sql = "INSERT INTO adotante (
    //             nome
    //             cpf
    //             data_nascimento
    //             cep
    //             estado
    //             cidade
    //             bairro
    //             logradouro
    //             numero
    //             complemento
    //             telefone_1
    //             telefone_2
    //             status
    //         ) VALUES (
    //             :nome
    //             :cpf
    //             :data_nascimento
    //             :cep
    //             :estado
    //             :cidade
    //             :bairro
    //             :logradouro
    //             :numero
    //             :complemento
    //             :telefone_1
    //             :telefone_2
    //             :status
    //         )";

    //         $stmt = $this->getConn()->prepare($sql);
    //         $stmt->bindValue('nome', $nome);
    //         $stmt->bindValue('cpf', $cpf);
    //         $stmt->bindValue('data_nascimento', $data_nascimento);
    //         $stmt->bindValue('cep', $cep);
    //         $stmt->bindValue('estado', $estado);
    //         $stmt->bindValue('cidade', $cidade);
    //         $stmt->bindValue('bairro', $bairro);
    //         $stmt->bindValue('logradouro', $logradouro);
    //         $stmt->bindValue('numero', $numero);
    //         $stmt->bindValue('complemento', $complemento);
    //         $stmt->bindValue('telefone_1', $telefone_1);
    //         $stmt->bindValue('telefone_2', $telefone_2);
    //         $stmt->bindValue('status', $status);
    //         $stmt->execute();
    //     } catch (\PDOException $ex) {
    //         header('Location:/error103');
    //         die();
    //     }
    // }

    public function inserir($obj)
    {
        try {
            $nome = $obj->__get("nome");
            $cpf = $obj->__get("cpf");
            $data_nascimento = $obj->__get("data_nascimento");
            $cep = $obj->__get("cep");
            $estado = $obj->__get("estado");
            $cidade = $obj->__get("cidade");
            $bairro = $obj->__get("bairro");
            $logradouro = $obj->__get("logradouro");
            $numero = $obj->__get("numero");
            $complemento = $obj->__get("complemento");
            $telefone_1 = $obj->__get("telefone_1");
            $telefone_2 = $obj->__get("telefone_2");
            $status = $obj->__get("status");


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
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':data_nascimento', $data_nascimento);
            $stmt->bindValue(':cep', $cep);
            $stmt->bindValue(':estado', $estado);
            $stmt->bindValue(':cidade', $cidade);
            $stmt->bindValue(':bairro', $bairro);
            $stmt->bindValue(':logradouro', $logradouro);
            $stmt->bindValue(':numero', $numero);
            $stmt->bindValue(':complemento', $complemento);
            $stmt->bindValue(':telefone_1', $telefone_1);
            $stmt->bindValue(':telefone_2', $telefone_2);
            $stmt->bindValue(':status', $status);
            $stmt->execute();
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public  function excluir($id)
    {
        $sql = "DELETE FROM adotante WHERE id = :id";

        $stmt = $this->getConn()->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
    }
    public  function alterar($obj)
    {
        try {
            $id = $obj->__get("id");
            $nome = $obj->__get("nome");
            $cpf = $obj->__get("cpf");
            $data_nascimento = $obj->__get("data_nascimento");
            $cep = $obj->__get("cep");
            $estado = $obj->__get("estado");
            $cidade = $obj->__get("cidade");
            $bairro = $obj->__get("bairro");
            $logradouro = $obj->__get("logradouro");
            $numero = $obj->__get("numero");
            $complemento = $obj->__get("complemento");
            $telefone_1 = $obj->__get("telefone_1");
            $telefone_2 = $obj->__get("telefone_2");
            $status = $obj->__get("status");

            $sql = "UPDATE adotante as a
                SET 
                nome = :nome,
                cpf = :cpf,
                data_nascimento = :data_nascimento,
                cep = :cep,
                estado = :estado,
                cidade = :cidade,
                bairro = :bairro,
                logradouro = :logradouro,
                numero = :numero,
                complemento = :complemento,
                telefone_1 = :telefone_1,
                telefone_2 = :telefone_2,
                `status` = :status
            WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':data_nascimento', $data_nascimento);
            $stmt->bindValue(':cep', $cep);
            $stmt->bindValue(':estado', $estado);
            $stmt->bindValue(':cidade', $cidade);
            $stmt->bindValue(':bairro', $bairro);
            $stmt->bindValue(':logradouro', $logradouro);
            $stmt->bindValue(':numero', $numero);
            $stmt->bindValue(':complemento', $complemento);
            $stmt->bindValue(':telefone_1', $telefone_1);
            $stmt->bindValue(':telefone_2', $telefone_2);
            $stmt->bindValue(':status', $status);
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
            WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($resultado > 0) {
                $adotanteModel = new AdotanteModel();

                $global = new FuncoesGlobais();
                $global->popularModel($adotanteModel, $resultado);

                return $adotanteModel;
            }

            return false;
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
    public function listar()
    {
        try {
            $adotantes = array();

            $sql = "SELECT 
                a.*,
                l.email
            FROM 
                adotante a,
            JOIN 
                login l 
            ON 
                a.fk_login_id = l.id
            ";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($resultado as $row) {
                $adotanteModel = new AdotanteModel();

                $global = new FuncoesGlobais();
                $global->popularModel($adotanteModel, $row);

                array_push($adotantes, $adotanteModel);
            }

            return $adotantes;
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
}
