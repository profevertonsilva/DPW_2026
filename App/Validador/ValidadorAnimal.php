<?php

namespace App\Validador;

use App\Model\AnimalModel;

/**
 * Validador para entidade Animal
 * 
 * @Author marcus rito
 * @Version 1.0
 */
class ValidadorAnimal extends Validador
{
    /**
     * Valida modelo de Animal completo
     *
     * @param AnimalModel $animal
     * @param bool $ehEdicao
     * @return bool
     */
    public function validar(AnimalModel $animal, bool $ehEdicao = false): bool
    {
        $this->limparErros();
        
        $nome = $animal->__get('nome');
        $sexo = $animal->__get('sexo');
        $especieId = $animal->__get('fk_especie_id');
        $porte = $animal->__get('porte');
        $status = $animal->__get('status');
        $dataNascimento = $animal->__get('data_nascimento');
        
        // Nome
        $this->validarNaoVazio($nome, 'nome');
        $this->validarComprimentoMinimo($nome, 3, 'nome');
        $this->validarComprimentoMaximo($nome, 100, 'nome');
        
        // Sexo (minúsculas conforme schema)
        $this->validarNaoVazio($sexo, 'sexo');
        $sexoMinuscula = strtolower($sexo);
        $this->validarEnumeracao($sexoMinuscula, ['m', 'f'], 'sexo');
        
        // Espécie (obrigatória)
        if (empty($especieId)) {
            $this->adicionarErro('fk_especie_id', 'Espécie deve ser selecionada');
        } else {
            $this->validarNumero($especieId, 'fk_especie_id');
        }
        
        // Porte (conforme schema: pequeno, medio, grande)
        $this->validarNaoVazio($porte, 'porte');
        $this->validarEnumeracao($porte, ['pequeno', 'medio', 'grande'], 'porte');
        
        // Status (conforme schema)
        if (!empty($status)) {
            $this->validarEnumeracao($status, ['disponivel', 'adotado', 'em_tratamento', 'reservado'], 'status');
        }
        
        // Data de Nascimento (opcional mas se preenchida, deve ser válida)
        if (!empty($dataNascimento)) {
            $this->validarData($dataNascimento, 'data_nascimento');
            
            // Verificar se não é data futura
            if (strtotime($dataNascimento) > time()) {
                $this->adicionarErro('data_nascimento', 'Data de nascimento não pode ser no futuro');
            }
        }
        
        return !$this->temErros();
    }
    
    /**
     * Valida apenas os campos do formulário de cadastro/edição
     *
     * @param array $dados
     * @return bool
     */
    public function validarFormulario(array $dados): bool
    {
        $this->limparErros();
        
        $nome = $dados['nome'] ?? '';
        $sexo = $dados['sexo'] ?? '';
        $especieId = $dados['fk_especie_id'] ?? '';
        $porte = $dados['porte'] ?? '';
        $status = $dados['status'] ?? 'disponivel';
        $dataNascimento = $dados['data_nascimento'] ?? '';
        
        // Nome: 3-100 caracteres, obrigatório
        $this->validarNaoVazio($nome, 'nome');
        if (!$this->temErros()) {
            $this->validarComprimentoMinimo($nome, 3, 'nome');
            $this->validarComprimentoMaximo($nome, 100, 'nome');
        }
        
        // Sexo: 'm' ou 'f' (minúsculas conforme schema)
        $this->validarNaoVazio($sexo, 'sexo');
        if (!$this->temErros()) {
            $sexoMinuscula = strtolower($sexo);
            $this->validarEnumeracao($sexoMinuscula, ['m', 'f'], 'sexo');
        }
        
        // Espécie: obrigatória (converter para int para validar)
        $especieIdInt = (int) $especieId;
        if (empty($especieId) || $especieIdInt <= 0) {
            $this->adicionarErro('fk_especie_id', 'Espécie deve ser selecionada');
        } else {
            $this->validarNumero($especieIdInt, 'fk_especie_id');
        }
        
        // Porte: pequeno, medio, grande (conforme schema, sem acento)
        $this->validarNaoVazio($porte, 'porte');
        if (!$this->temErros()) {
            $this->validarEnumeracao($porte, ['pequeno', 'medio', 'grande'], 'porte');
        }
        
        // Data de Nascimento: opcional, mas se preenchida não pode ser futura
        if (!empty($dataNascimento)) {
            $this->validarData($dataNascimento, 'data_nascimento');
            
            // Se passou na validação de formato, verifica se é futura (comparação apenas de datas)
            if (!$this->temErros()) {
                $dataAtual = date('Y-m-d');
                if ($dataNascimento > $dataAtual) {
                    $this->adicionarErro('data_nascimento', 'Data de nascimento não pode ser no futuro');
                }
            }
        }
        
        // Status: disponivel, adotado, em_tratamento, reservado (conforme schema)
        if (!empty($status)) {
            $this->validarEnumeracao($status, ['disponivel', 'adotado', 'em_tratamento', 'reservado'], 'status');
        }
        
        return !$this->temErros();
    }
}
