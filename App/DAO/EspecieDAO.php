<?php
 
namespace App\DAO;
 
use App\DAO;
use App\Model\EspecieModel;
use FW\Controller\FuncoesGlobais;
 
class EspecieDAO extends DAO
{
    public function inserir($obj)
    {
        try {
            $nome = $obj->__get('nome');
 
            $sql = "INSERT INTO especie (nome) VALUES (:nome)";
 
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->execute();
 
            return (int) $this->getConn()->lastInsertId();
 
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
 
    public function excluir($id)
    {
        try {
            $sql = "DELETE FROM especie WHERE id = :id";
 
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
 
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
 
    public function alterar($obj)
    {
        try {
            $id   = $obj->__get('id');
            $nome = $obj->__get('nome');
 
            $sql = "UPDATE especie SET nome = :nome WHERE id = :id";
 
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id',   $id,   \PDO::PARAM_INT);
            $stmt->bindValue(':nome', $nome);
            $stmt->execute();
 
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
 
    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT * FROM especie WHERE id = :id";
 
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
 
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
 
            if ($resultado !== false) {
                $model  = new EspecieModel();
                $global = new FuncoesGlobais();
                $global->popularModel($model, $resultado);
                return $model;
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
            $especies = array();
 
            $sql = "SELECT * FROM especie ORDER BY nome ASC";
 
            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();
 
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
 
            foreach ($resultado as $row) {
                $model  = new EspecieModel();
                $global = new FuncoesGlobais();
                $global->popularModel($model, $row);
                array_push($especies, $model);
            }
 
            return $especies;
 
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
}
