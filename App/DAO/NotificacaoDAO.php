<?php

namespace App\DAO;

use App\DAO;
use App\Model\Notificacao;
use FW\Controller\FuncoesGlobais;

class NotificacaoDAO extends DAO
{
    public function inserir($obj)
    {
        try {
            $fk_login_id    = $obj->__get("fk_login_id");
            $titulo         = $obj->__get("titulo");
            $mensagem       = $obj->__get("mensagem");
            $tipo           = $obj->__get("tipo");
            $destino_tab    = $obj->__get("destino_tab");
            $destino_tela   = $obj->__get("destino_tela");
            $destino_params = $obj->__get("destino_params");

            $sql = "INSERT INTO notificacao (
                        fk_login_id,
                        titulo,
                        mensagem,
                        tipo,
                        destino_tab,
                        destino_tela,
                        destino_params
                    ) VALUES (
                        :fk_login_id,
                        :titulo,
                        :mensagem,
                        :tipo,
                        :destino_tab,
                        :destino_tela,
                        :destino_params
                    )";

            $conn = $this->getConn();
            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':fk_login_id', $fk_login_id, \PDO::PARAM_INT);
            $stmt->bindValue(':titulo', $titulo);
            $stmt->bindValue(':mensagem', $mensagem);
            $stmt->bindValue(':tipo', $tipo);
            $stmt->bindValue(':destino_tab', $destino_tab);
            $stmt->bindValue(':destino_tela', $destino_tela);
            $stmt->bindValue(':destino_params', $destino_params);

            $stmt->execute();

            return $conn->lastInsertId();

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function listarPorUsuario($fk_login_id)
    {
        try {

            $notificacoes = array();

            $sql = "SELECT *
                    FROM notificacao
                    WHERE fk_login_id = :fk_login_id
                    ORDER BY data DESC";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':fk_login_id', $fk_login_id, \PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($resultado as $row) {
                $model = new Notificacao();
                $global = new FuncoesGlobais();
                $global->popularModel($model, $row);

                array_push($notificacoes, $model);
            }

            return $notificacoes;

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function marcarComoLida($id)
    {
        try {

            $sql = "UPDATE notificacao
                    SET lida = 1
                    WHERE id = :id";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

            $stmt->execute();

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function contarNaoLidas($fk_login_id)
    {
        try {

            $sql = "SELECT COUNT(*) AS total
                    FROM notificacao
                    WHERE fk_login_id = :fk_login_id
                    AND lida = 0";

            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':fk_login_id', $fk_login_id, \PDO::PARAM_INT);

            $stmt->execute();

            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);

            return (int)$resultado['total'];

        } catch (\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }
}