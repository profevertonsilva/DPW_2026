<?php

namespace App\Logger;

/**
 * Classe Logger para rastreamento de erros e operações
 * Mantém histórico de erros e ações importantes no sistema
 * 
 * @Author marcus rito
 * @Version 1.0
 */
class Logger
{
    private string $logDir = __DIR__ . '/../../storage/logs/';
    private string $logFile;
    
    public function __construct()
    {
        // Criar diretório de logs se não existir
        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, 0755, true);
        }
        
        $this->logFile = $this->logDir . date('Y-m-d') . '.log';
    }
    
    /**
     * Registra informação
     *
     * @param string $mensagem
     * @param array $contexto
     * @return void
     */
    public function info(string $mensagem, array $contexto = []): void
    {
        $this->escrever('INFO', $mensagem, $contexto);
    }
    
    /**
     * Registra aviso
     *
     * @param string $mensagem
     * @param array $contexto
     * @return void
     */
    public function warning(string $mensagem, array $contexto = []): void
    {
        $this->escrever('WARNING', $mensagem, $contexto);
    }
    
    /**
     * Registra erro
     *
     * @param string $mensagem
     * @param array $contexto
     * @return void
     */
    public function erro(string $mensagem, array $contexto = []): void
    {
        $this->escrever('ERRO', $mensagem, $contexto);
    }
    
    /**
     * Registra erro crítico
     *
     * @param string $mensagem
     * @param array $contexto
     * @return void
     */
    public function critico(string $mensagem, array $contexto = []): void
    {
        $this->escrever('CRÍTICO', $mensagem, $contexto);
    }
    
    /**
     * Escreve a mensagem no arquivo de log
     *
     * @param string $nivel
     * @param string $mensagem
     * @param array $contexto
     * @return void
     */
    private function escrever(string $nivel, string $mensagem, array $contexto = []): void
    {
        try {
            $timestamp = date('Y-m-d H:i:s');
            $contextoStr = !empty($contexto) ? json_encode($contexto, JSON_UNESCAPED_UNICODE) : '';
            
            $linha = "[{$timestamp}] [{$nivel}] {$mensagem}";
            
            if ($contextoStr) {
                $linha .= " | Contexto: {$contextoStr}";
            }
            
            $linha .= PHP_EOL;
            
            // Adicionar informação do usuário/IP se disponível
            $usuario = $_SESSION['usuario_id'] ?? 'anônimo';
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
            $linha = str_replace(PHP_EOL, " | Usuário: {$usuario} | IP: {$ip}" . PHP_EOL, $linha);
            
            file_put_contents($this->logFile, $linha, FILE_APPEND);
        } catch (\Exception $ex) {
            // Falha silenciosa para não quebrar a aplicação
            error_log("Falha ao escrever log: " . $ex->getMessage());
        }
    }
    
    /**
     * Retorna as últimas N linhas do arquivo de log
     *
     * @param int $linhas
     * @return array
     */
    public function obterUltimas(int $linhas = 50): array
    {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $handle = fopen($this->logFile, 'r');
        $conteudo = [];
        
        while (($linha = fgets($handle)) !== false) {
            $conteudo[] = trim($linha);
        }
        
        fclose($handle);
        
        return array_slice($conteudo, -$linhas);
    }
}
