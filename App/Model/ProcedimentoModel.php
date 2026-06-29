<?php
/*
 * Modelo para RF#16 - Sistema de Gerenciamento de Procedimentos Médicos
 *
 * Tabela: procedimento
 * Schema:
 *   id | nome | tipo | data | veterinario_nome | observacoes
 *   anexo_url | fk_animal_id | fk_login_id | criado_em | criado_por
 *
 * Política: append-only (RNF#08) — registros não podem ser excluídos
 */

namespace App\Model;

class ProcedimentoModel
{
    private $id;
    private $nome;
    // Enum (consulta, cirurgia, exame, castracao, outro)
    private $tipo;
    private $data;
    private $veterinario_nome;
    private $observacoes;
    private $anexo_url;

    // FK para tabela animal
    private $fk_animal_id;

    // FK para login (usuário que registrou)
    private $fk_login_id;

    // Metadados de auditoria (populados pelo banco)
    private $criado_em;
    private $criado_por;

    // Campos extras vindos de JOINs (populados pelo FuncoesGlobais->popularModel)
    private $animal_nome; // JOIN com tabela animal

    public function __get($nome)
    {
        return $this->$nome;
    }

    public function __set($nome, $valor)
    {
        $this->$nome = $valor;
    }
}
