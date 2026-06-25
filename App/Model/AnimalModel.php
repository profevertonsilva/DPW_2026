<?php
/*
 * @Author marcus rito
 * Refatorado para compatibilidade dinâmica AmigoPet - 2026
 */

namespace App\Model;

class AnimalModel
{
    private $id;
    private $nome;
    private $sexo;
    private $data_nascimento;

    // Inserção dos novos campos
    private $cor;
    private $porte;
    private $status;
    private $castrado;
    private $foto;
    private $descricao;

    // Mapeamento extra para o caso do banco de dados trazer colunas prefixadas (ex: ani_nome)
    public function __set($nome, $valor) {
        // Se vier do banco como 'ani_nome', removemos o prefixo para encaixar na propriedade limpa
        $nomeLimpo = str_replace('ani_', '', $nome);
        
        if (property_exists($this, $nomeLimpo)) {
            $this->$nomeLimpo = $valor;
        } else {
            $this->$nome = $valor;
        }
    }

    public function __get($nome) {
        $nomeLimpo = str_replace('ani_', '', $nome);
        
        if (property_exists($this, $nomeLimpo)) {
            return $this->$nomeLimpo;
        }
        return $this->$nome;
    }
}