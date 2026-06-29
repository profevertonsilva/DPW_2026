<?php

namespace App\DAO;

use App\DAO;
use App\Model\PublicacaoEncontrado;
use FW\Controller\FuncoesGlobais;

class PublicacaoEncontradoDAO extends DAO
{
    public function inserir($obj)
    {
        try {
            $fk_animal_id    = $obj->__get("fk_animal_id");
            $fk_login_id     = $obj->__get("fk_login_id");
            $data_encontro   = $obj->__get("data_encontro");
            $condicao_fisica  = $obj->__get("condicao_fisica");
            $acoes_realizadas= $obj->__get("acoes_realizadas");
            $status          = $obj->__get("status");

            $sql = "INSERT INTO publicacao_encontrado (
                fk_animal_id,
                fk_login_id,
                data_encontro,
                condicao_fisica,
                acoes_realizadas,
                status
            ) VALUES (
                :fk_animal_id,
                :fk_login_id,
                :data_encontro,
                :condicao_fisica,
                :acoes_realizadas,
                :status
            )";

            $conn = $this->getConn();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':fk_animal_id', $fk_animal_id, \PDO::PARAM_INT);
            $stmt->bindValue(':fk_login_id', $fk_login_id, \PDO::PARAM_INT);
            $stmt->bindValue(':data_encontro', $data_encontro);
            $stmt->bindValue(':condicao_fisica', $condicao_fisica);
            $stmt->bindValue(':acoes_realizadas', $acoes_realizadas);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            return $conn->lastInsertId();
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function excluir($id)
    {
        try {
            $sql = "DELETE FROM publicacao_encontrado WHERE id = :id";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(":id", $id);
            $stmt->execute();
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function alterar($obj)
    {
        try {
            $id              = $obj->__get("id");
            $fk_animal_id      = $obj->__get("fk_animal_id");
            $data_encontro   = $obj->__get("data_encontro");
            $condicao_fisica  = $obj->__get("condicao_fisica");
            $acoes_realizadas= $obj->__get("acoes_realizadas");
            $status          = $obj->__get("status");

            $sql = "UPDATE publicacao_encontrado
                SET 
                fk_animal_id     = :fk_animal_id,
                data_encontro   = :data_encontro,
                condicao_fisica  = :condicao_fisica,
                acoes_realizadas= :acoes_realizadas,
                status          = :status
            WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->bindValue(':fk_animal_id', $fk_animal_id, \PDO::PARAM_INT);
            $stmt->bindValue(':data_encontro', $data_encontro);
            $stmt->bindValue(':condicao_fisica', $condicao_fisica);
            $stmt->bindValue(':acoes_realizadas', $acoes_realizadas);
            $stmt->bindValue(':status', $status);
            $stmt->execute();
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT
            p.*,
            l.email,
            a.nome AS animal_nome
            FROM publicacao_encontrado p
            JOIN login l ON p.fk_login_id = l.id
            JOIN animal a ON p.fk_animal_id = a.id
            WHERE p.id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($resultado) {
                $model = new PublicacaoEncontrado();
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
            $publicacoes = array();
          
            $sql = "SELECT
            p.*,
            an.nome AS animal_nome,
            ad.nome AS usuario_nome
            FROM publicacao_encontrado p
            JOIN animal an
            ON an.id = p.fk_animal_id
            JOIN login l
            ON l.id = p.fk_login_id
            JOIN adotante ad
            ON ad.fk_login_id = l.id
            ORDER BY p.id DESC";
            
            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($resultado as $row) {
                $model = new PublicacaoEncontrado();
                $global = new FuncoesGlobais();
                $global->popularModel($model, $row);
                array_push($publicacoes, $model);
            }

            return $publicacoes;
        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
}
