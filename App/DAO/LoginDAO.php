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
            error_log("Error in LoginDAO::listar: " . $ex->getMessage());
            throw $ex;
        }
    }

    public function atualizarTipoUsuario($id, $tipoUsuario)
    {
        try {
            $conn = $this->getConn();

            // Verificar se o ID existe antes de atualizar
            $checkExistsSql = "SELECT id, tipo_usuario FROM login WHERE id = :id";
            $checkExistsStmt = $conn->prepare($checkExistsSql);
            $checkExistsStmt->bindValue(':id', $id);
            $checkExistsStmt->execute();
            $existsResult = $checkExistsStmt->fetch(\PDO::FETCH_ASSOC);

            error_log("Verificação de existência: ID=$id, Existe=" . ($existsResult ? 'SIM' : 'NÃO') . ", Valor atual=" . ($existsResult['tipo_usuario'] ?? 'NULL'));

            if (!$existsResult) {
                error_log("ERRO: ID=$id não encontrado na tabela login");
                return false;
            }

            // Check current enum values in the database
            $enumCheckSql = "SHOW COLUMNS FROM login LIKE 'tipo_usuario'";
            $enumStmt = $conn->prepare($enumCheckSql);
            $enumStmt->execute();
            $enumResult = $enumStmt->fetch(\PDO::FETCH_ASSOC);
            error_log("Enum definition: " . ($enumResult['Type'] ?? 'NULL'));

            $sql = "UPDATE login SET tipo_usuario = :tipoUsuario WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':tipoUsuario', $tipoUsuario);

            // Log antes da execução
            error_log("Antes da execução: ID=$id, Tipo=$tipoUsuario, SQL=$sql");
            error_log("TipoUsuario length=" . strlen($tipoUsuario) . ", bytes=" . bin2hex($tipoUsuario));

            $result = $stmt->execute();

            // Log após a execução
            error_log("Após execução: Resultado=" . ($result ? 'true' : 'false') . ", rowCount=" . $stmt->rowCount());

            // Verificar o valor atual no banco
            $checkSql = "SELECT tipo_usuario FROM login WHERE id = :id";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bindValue(':id', $id);
            $checkStmt->execute();
            $checkResult = $checkStmt->fetch(\PDO::FETCH_ASSOC);

            error_log("Valor no banco após atualização: " . ($checkResult['tipo_usuario'] ?? 'NULL'));

            return true;
        } catch (\PDOException $ex) {
            error_log("PDOException na atualização: " . $ex->getMessage());
            throw $ex;
        }
    }
}
