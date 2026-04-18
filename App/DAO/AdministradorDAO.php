<?php 

namespace App\DAO;

use App\DAO;
use App\Model\AdministradorModel;
use FW\Controller\FuncoesGlobais;

class AdministradorDAO extends DAO {
    public function inserir($obj) {
        try {
             $adm_nome =  $obj->__get('adm_nome');
             $adm_cpf = $obj->__get('adm_cpf');
             $adm_cep = $obj->__get('adm_cep');
             $adm_logradouro = $obj->__get('adm_logradouro');
             $adm_estado = $obj->__get('adm_estado');
             $adm_complemento = $obj->__get('adm_complemento');
             $adm_dn = $obj->__get('adm_dn');
             $adm_cidade = $obj->__get('adm_cidade');
             $adm_bairro = $obj->__get('adm_bairro');
             $adm_numero = $obj->__get('adm_numero'); // adicionar no bd
             $adm_tel1 = $obj->__get('adm_tel1');
             $adm_tel2 = $obj->__get('adm_tel2');

            $sql = "INSERT INTO administrador (
                        adm_nome,
                        adm_cpf,
                        adm_cep,
                        adm_logradouro,
                        adm_estado,
                        adm_complemento,
                        adm_dn,
                        adm_cidade,
                        adm_bairro,
                        adm_numero,
                        adm_tel1,
                        adm_tel2
                    ) VALUES (
                        :adm_nome,
                        :adm_cpf,
                        :adm_cep,
                        :adm_logradouro,
                        :adm_estado,
                        :adm_complemento,
                        :adm_dn,
                        :adm_cidade,
                        :adm_bairro,
                        :adm_numero,
                        :adm_tel1,
                        :adm_tel2
                    )";
                      $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':adm_nome', $adm_nome);
            $stmt->bindValue(':adm_cpf', $adm_cpf);
            $stmt->bindValue(':adm_cep', $adm_cep);
            $stmt->bindValue(':adm_logradouro', $adm_logradouro);
            $stmt->bindValue(':adm_estado', $adm_estado);
            $stmt->bindValue(':adm_complemento', $adm_complemento);
            $stmt->bindValue(':adm_dn', $adm_dn);
            $stmt->bindValue(':adm_cidade', $adm_cidade);
            $stmt->bindValue(':adm_numero', $adm_numero);  
            $stmt->bindValue(':adm_tel1', $adm_tel1);
            $stmt->bindValue(':adm_tel2', $adm_tel2);
            $stmt->execute();
        }
        catch(\PDOException $ex) {
            header('Location:/error103');
            die();
        }
    }

    public function listar(){
    try{
            $administrador = array();
            $sql = "SELECT 
                            a.*, 
                            l.log_email 
                        FROM 
                            administrador a,
                            login l
                        WHERE
                            ad.fk_login_log_id = l.log_id
                    ";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach($result as $row){
                $administradorModel = new AdministradorModel();
                
                $global = new FuncoesGlobais();
                $global->popularModel($administradorModel, $row);

                array_push($administrador, $administradorModel);
            }
            return $administrador;
        }
        catch(\PDOException $ex){
            header('Location:/error103');
            die();
        }    
    }

    public function excluir($obj){
    try{
        $sql = "DELETE FROM administrador WHERE adm_id = :id";
        
        $stmt = $this->getConn()->prepare($sql);
        $stmt->bindValue(":id", $obj->getId());
        $stmt->execute();

        return true;
    }
    catch(\PDOException $ex){
        header('Location:/error103');
        die();
    }
}
    public function alterar($obj){
    try{
        $sql = "UPDATE administrador SET 
                    adm_nome = :nome,
                    fk_login_log_id = :login
                WHERE 
                    adm_id = :id";

        $stmt = $this->getConn()->prepare($sql);
        $stmt->bindValue(":nome", $obj->getNome());
        $stmt->bindValue(":login", $obj->getLoginId());
        $stmt->bindValue(":id", $obj->getId());

        $stmt->execute();

        return true;
    }
    catch(\PDOException $ex){
        header('Location:/error103');
        die();
    }
}
    public function buscarPorId($id){
    try{
        $sql = "SELECT 
                    a.*, 
                    l.log_email
                FROM 
                    administrador a,
                    login l
                WHERE 
                    a.fk_login_log_id = l.log_id
                    AND a.adm_id = :id";

        $stmt = $this->getConn()->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row){
            $administradorModel = new AdministradorModel();
            
            $global = new FuncoesGlobais();
            $global->popularModel($administradorModel, $row);

            return $administradorModel;
        }

        return null;
    }
    catch(\PDOException $ex){
        header('Location:/error103');
        die();
    }
}
    
}