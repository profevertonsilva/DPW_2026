<?php

namespace App\Validador;

use App\Model\RacaModel;

/**
 * Validador para entidade Raca
 * 
 * @Author marcus rito
 * @Version 1.0
 */
class ValidadorRaca extends Validador
{
    /**
     * Valida modelo de Raca
     *
     * @param RacaModel $raca
     * @return bool
     */
    public function validar(RacaModel $raca): bool
    {
        $this->limparErros();
        
        $nome = $raca->__get('nome');
        $especieId = $raca->__get('fk_especie_id') ?? '';
        $descricao = $raca->__get('descricao') ?? '';
        
        // Nome
        $this->validarNaoVazio($nome, 'nome');
        $this->validarComprimentoMinimo($nome, 3, 'nome');
        $this->validarComprimentoMaximo($nome, 50, 'nome');
        
        // Espécie (obrigatória para Raça)
        if (empty($especieId)) {
            $this->adicionarErro('fk_especie_id', 'Espécie deve ser selecionada');
        } else {
            $this->validarNumero($especieId, 'fk_especie_id');
        }
        
        // Descrição (opcional)
        if (!empty($descricao)) {
            $this->validarComprimentoMaximo($descricao, 255, 'descricao');
        }
        
        return !$this->temErros();
    }
    
    /**
     * Valida apenas os campos do formulário
     *
     * @param array $dados
     * @return bool
     */
    public function validarFormulario(array $dados): bool
    {
        $this->limparErros();
        
        $nome = $dados['nome'] ?? '';
        $especieId = $dados['fk_especie_id'] ?? '';
        $descricao = $dados['descricao'] ?? '';
        
        $this->validarNaoVazio($nome, 'nome');
        if (!$this->temErros()) {
            $this->validarComprimentoMinimo($nome, 3, 'nome');
            $this->validarComprimentoMaximo($nome, 50, 'nome');
        }
        
        // Espécie (obrigatória) - converter para int para validar corretamente
        $especieIdInt = (int) $especieId;
        if (empty($especieId) || $especieIdInt <= 0) {
            $this->adicionarErro('fk_especie_id', 'Espécie deve ser selecionada');
        } else {
            $this->validarNumero($especieIdInt, 'fk_especie_id');
        }
        
        if (!empty($descricao)) {
            $this->validarComprimentoMaximo($descricao, 255, 'descricao');
        }
        
        return !$this->temErros();
    }
}
