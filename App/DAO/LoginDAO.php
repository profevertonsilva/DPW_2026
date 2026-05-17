<?php

namespace App\DAO;

use App\DAO;
use App\Model\LoginModel;
use FW\Controller\FuncoesGlobais;

class LoginDAO extends DAO
{

    public function inserir($obj)
    {
        try {

            $email = $obj->__get('email');
            $senha = $obj->__get('senha');
            $status = $obj->__get('status');
            $tipo_usuario = $obj->__get('tipo_usuario');

            $sql = "INSERT INTO login (
                email,
                senha,
                status,
                tipo_usuario
            ) VALUES (
                :email,
                :senha,
                :status,
                :tipo_usuario
            )";

            $conn = $this->getConn();

            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':senha', password_hash($senha, PASSWORD_DEFAULT));
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':tipo_usuario', $tipo_usuario);
            $stmt->execute();

            return $conn->lastInsertId();
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public  function excluir($id)
    {
        try {
            $sql = "DELETE FROM login WHERE id = :id";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(":id", $id);
            $stmt->execute();
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
    public  function alterar($obj)
    {
        try {
            $id = $obj->__get("id");
            $email = $obj->__get("email");
            $senha = $obj->__get("senha");
            $status = $obj->__get("status") ?: 'a';
            $tipo_usuario = $obj->__get("tipo_usuario") ?: 'adotante';

            $sql = "UPDATE 
                login
            SET 
                email = :email,
                status = :status,
                tipo_usuario = :tipo_usuario
            WHERE 
                id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':senha', password_hash($senha, PASSWORD_DEFAULT));
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':tipo_usuario', $tipo_usuario);
            $stmt->execute();
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function alterarSenha($obj) {
        try {

            $id = $obj->__get('id');
            $senha = $obj->__get('senha');

            $sql = "UPDATE 
                login
            SET 
                senha = :senha
            WHERE 
                id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue('id', $id);
            $stmt->bindValue('senha', password_hash($senha, PASSWORD_DEFAULT));
            $stmt->execute();


        }catch(\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public  function buscarPorEmail($email)
    {
        try {
            $sql = "SELECT * 
            FROM 
                login
            WHERE 
                email = :email";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($resultado) {
                $loginModel = new LoginModel();

                $global = new FuncoesGlobais();
                $global->popularModel($loginModel, $resultado);

                return $loginModel;
            }

            return false;
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
    public  function buscarPorId($id)
    {
        try {
            $sql = "SELECT * 
            FROM login
            WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($resultado) {
                $loginModel = new LoginModel();

                $global = new FuncoesGlobais();
                $global->popularModel($loginModel, $resultado);

                return $loginModel;
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
            $logins = array();

            $sql = "SELECT 
                id,
                email,
                status,
                tipo_usuario,
                data_cadastro,
                data_atualizacao
            FROM 
                login 
            ";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($resultado as $row) {
                $loginModel = new LoginModel();

                $global = new FuncoesGlobais();
                $global->popularModel($loginModel, $row);

                array_push($logins, $loginModel);
            }

            return $logins;
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
}
