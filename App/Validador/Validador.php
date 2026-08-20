<?php

namespace App\Validador;

/**
 * Classe base para validadores
 * Define padrão para validação de dados
 * 
 * @Author marcus rito
 * @Version 1.0
 */
abstract class Validador
{
    protected array $erros = [];
    
    /**
     * Retorna array de erros
     *
     * @return array
     */
    public function obterErros(): array
    {
        return $this->erros;
    }
    
    /**
     * Verifica se há erros
     *
     * @return bool
     */
    public function temErros(): bool
    {
        return !empty($this->erros);
    }
    
    /**
     * Adiciona erro à lista
     *
     * @param string $campo
     * @param string $mensagem
     * @return void
     */
    protected function adicionarErro(string $campo, string $mensagem): void
    {
        $this->erros[$campo][] = $mensagem;
    }
    
    /**
     * Valida se campo é vazio
     *
     * @param string $valor
     * @param string $nomeCampo
     * @return bool
     */
    protected function validarNaoVazio(string $valor, string $nomeCampo): bool
    {
        if (empty(trim($valor))) {
            $this->adicionarErro($nomeCampo, "{$nomeCampo} não pode estar vazio");
            return false;
        }
        return true;
    }
    
    /**
     * Valida comprimento mínimo
     *
     * @param string $valor
     * @param int $minimo
     * @param string $nomeCampo
     * @return bool
     */
    protected function validarComprimentoMinimo(string $valor, int $minimo, string $nomeCampo): bool
    {
        if (strlen(trim($valor)) < $minimo) {
            $this->adicionarErro($nomeCampo, "{$nomeCampo} deve ter no mínimo {$minimo} caracteres");
            return false;
        }
        return true;
    }
    
    /**
     * Valida comprimento máximo
     *
     * @param string $valor
     * @param int $maximo
     * @param string $nomeCampo
     * @return bool
     */
    protected function validarComprimentoMaximo(string $valor, int $maximo, string $nomeCampo): bool
    {
        if (strlen($valor) > $maximo) {
            $this->adicionarErro($nomeCampo, "{$nomeCampo} não pode exceder {$maximo} caracteres");
            return false;
        }
        return true;
    }
    
    /**
     * Valida se é um número
     *
     * @param mixed $valor
     * @param string $nomeCampo
     * @return bool
     */
    protected function validarNumero($valor, string $nomeCampo): bool
    {
        if (!is_numeric($valor) || $valor <= 0) {
            $this->adicionarErro($nomeCampo, "{$nomeCampo} deve ser um número válido");
            return false;
        }
        return true;
    }
    
    /**
     * Valida data no formato Y-m-d
     *
     * @param string $data
     * @param string $nomeCampo
     * @return bool
     */
    protected function validarData(string $data, string $nomeCampo): bool
    {
        $formato = 'Y-m-d';
        $d = \DateTime::createFromFormat($formato, $data);
        
        if (!$d || $d->format($formato) !== $data) {
            $this->adicionarErro($nomeCampo, "{$nomeCampo} deve estar no formato YYYY-MM-DD");
            return false;
        }
        return true;
    }
    
    /**
     * Valida se está em lista de valores permitidos
     *
     * @param string $valor
     * @param array $valores
     * @param string $nomeCampo
     * @return bool
     */
    protected function validarEnumeracao(string $valor, array $valores, string $nomeCampo): bool
    {
        if (!in_array($valor, $valores, true)) {
            $this->adicionarErro($nomeCampo, "{$nomeCampo} contém valor inválido");
            return false;
        }
        return true;
    }
    
    /**
     * Limpa os erros
     *
     * @return void
     */
    public function limparErros(): void
    {
        $this->erros = [];
    }
}
