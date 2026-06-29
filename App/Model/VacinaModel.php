<?php
/*
 * Modelo para RF — Sistema de Gerenciamento da Carteira de Vacinação
 *
 * Tabela: vacina
 * Política: append-only (RNF#08) — registros não podem ser excluídos
 */

namespace App\Model;

class VacinaModel
{
    private $id;
    private $nome;
    private $data_aplicacao;
    private $data_reforco;
    private $veterinario_nome;
    private $clinica_nome;

    // FK para tabela animal
    private $fk_animal_id;

    // FK para login (usuário que registrou)
    private $fk_login_id;

    // Metadados de auditoria (populados pelo banco)
    private $criado_em;
    private $criado_por;

    // Campos extras vindos de JOINs (populados pelo FuncoesGlobais->popularModel)
    private $animal_nome;  // JOIN com tabela animal

    public function __get($nome)
    {
        return $this->$nome;
    }

    public function __set($nome, $valor)
    {
        $this->$nome = $valor;
    }
}
