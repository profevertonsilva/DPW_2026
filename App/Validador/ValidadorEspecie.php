<?php

namespace App\Validador;

use App\Model\EspecieModel;

/**
 * Validador para entidade Especie
 * 
 * @Author marcus rito
 * @Version 1.0
 */
class ValidadorEspecie extends Validador
{
    /**
     * Valida modelo de Especie
     *
     * @param EspecieModel $especie
     * @return bool
     */
    public function validar(EspecieModel $especie): bool
    {
        $this->limparErros();
        
        $nome = $especie->__get('nome');
        $descricao = $especie->__get('descricao') ?? '';
        
        // Nome
        $this->validarNaoVazio($nome, 'nome');
        $this->validarComprimentoMinimo($nome, 3, 'nome');
        $this->validarComprimentoMaximo($nome, 50, 'nome');
        
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
        $descricao = $dados['descricao'] ?? '';
        
        $this->validarNaoVazio($nome, 'nome');
        if (!$this->temErros()) {
            $this->validarComprimentoMinimo($nome, 3, 'nome');
            $this->validarComprimentoMaximo($nome, 50, 'nome');
        }
        
        if (!empty($descricao)) {
            $this->validarComprimentoMaximo($descricao, 255, 'descricao');
        }
        
        return !$this->temErros();
    }
}
