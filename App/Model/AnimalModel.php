<?php
/*
 * @Author marcus rito
 * Atualizado Sprint 2
 * Adicionados: fk_especie_id, cor, castrado, descricao, localizacao
 * Removido: especie varchar (substituído por fk_especie_id com integridade referencial)
 */
namespace App\Model;

class AnimalModel
{
    private $id;
    private $nome;
    private $data_nascimento;
    private $sexo;

    // FK para tabela especie (substitui especie VARCHAR livre)
    private $fk_especie_id;

    // Campos adicionados Sprint 2
    private $cor;
    private $castrado;
    private $descricao;

    // Campos do schema original (ALTER Sprint 1)
    private $porte;
    private $localizacao;
    private $foto;
    private $status;

    // Campos extras vindos de JOINs (populados pelo FuncoesGlobais->popularModel)
    private $especie_nome;  // JOIN com tabela especie
    private $ong_id;        // JOIN via ong_animal
    private $ong_nome;      // JOIN via ong_animal → ong
    private $racas;         // GROUP_CONCAT via animal_raca → raca
    private $idade_meses;   // TIMESTAMPDIFF calculado no SELECT

    public function __get($nome)
    {
        return $this->$nome;
    }

    public function __set($nome, $valor)
    {
        $this->$nome = $valor;
    }
}