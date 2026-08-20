<?php

namespace App\Validador;

/**
 * Validador para uploads de arquivos
 * Valida tamanho, tipo MIME e segurança
 * 
 * @Author marcus rito
 * @Version 1.0
 */
class ValidadorUpload extends Validador
{
    // Tamanho máximo de upload em bytes (5MB)
    private int $tamanhoMaximo = 5242880;
    
    // Tipos MIME permitidos
    private array $tiposPermitidos = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif'
    ];
    
    /**
     * Define tamanho máximo de upload
     *
     * @param int $bytes
     * @return self
     */
    public function setTamanhoMaximo(int $bytes): self
    {
        $this->tamanhoMaximo = $bytes;
        return $this;
    }
    
    /**
     * Define tipos MIME permitidos
     *
     * @param array $tipos
     * @return self
     */
    public function setTiposPermitidos(array $tipos): self
    {
        $this->tiposPermitidos = $tipos;
        return $this;
    }
    
    /**
     * Valida arquivo de upload
     *
     * @param array $arquivo $_FILES['key']
     * @param string $nomeCampo
     * @return bool
     */
    public function validar(array $arquivo, string $nomeCampo = 'arquivo'): bool
    {
        $this->limparErros();
        
        // Verificar se arquivo foi enviado
        if (empty($arquivo) || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
            return true; // Upload opcional
        }
        
        // Verificar erros de upload
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            $this->adicionarErro($nomeCampo, $this->obterMensagemErroUpload($arquivo['error']));
            return false;
        }
        
        // Validar nome do arquivo
        if (!isset($arquivo['name']) || empty(trim($arquivo['name']))) {
            $this->adicionarErro($nomeCampo, 'Nome do arquivo inválido');
            return false;
        }
        
        // Validar caminho temporário
        if (!isset($arquivo['tmp_name']) || !is_uploaded_file($arquivo['tmp_name'])) {
            $this->adicionarErro($nomeCampo, 'Arquivo não foi enviado de forma segura');
            return false;
        }
        
        // Validar tamanho
        if (!$this->validarTamanho($arquivo['size'])) {
            $this->adicionarErro($nomeCampo, "Tamanho do arquivo não pode exceder " . $this->formatarTamanho($this->tamanhoMaximo));
            return false;
        }
        
        // Validar tipo MIME
        if (!$this->validarMIME($arquivo['tmp_name'])) {
            $this->adicionarErro($nomeCampo, "Tipo de arquivo não permitido");
            return false;
        }
        
        // Validar extensão
        if (!$this->validarExtensao($arquivo['name'])) {
            $this->adicionarErro($nomeCampo, "Extensão de arquivo não permitida");
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida tamanho do arquivo
     *
     * @param int $tamanho
     * @return bool
     */
    private function validarTamanho(int $tamanho): bool
    {
        return $tamanho <= $this->tamanhoMaximo;
    }
    
    /**
     * Valida tipo MIME do arquivo
     *
     * @param string $caminhoTemporario
     * @return bool
     */
    private function validarMIME(string $caminhoTemporario): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $caminhoTemporario);
        finfo_close($finfo);
        
        return in_array($mime, $this->tiposPermitidos);
    }
    
    /**
     * Valida extensão do arquivo
     *
     * @param string $nomeArquivo
     * @return bool
     */
    private function validarExtensao(string $nomeArquivo): bool
    {
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));
        
        return in_array($extensao, $extensoesPermitidas);
    }
    
    /**
     * Obtém mensagem de erro de upload
     *
     * @param int $codigoErro
     * @return string
     */
    private function obterMensagemErroUpload(int $codigoErro): string
    {
        $mensagens = [
            UPLOAD_ERR_INI_SIZE   => 'Arquivo excede o tamanho máximo permitido pela configuração do servidor',
            UPLOAD_ERR_FORM_SIZE  => 'Arquivo excede o tamanho máximo permitido pelo formulário',
            UPLOAD_ERR_PARTIAL    => 'Arquivo foi enviado parcialmente',
            UPLOAD_ERR_NO_FILE    => 'Nenhum arquivo foi enviado',
            UPLOAD_ERR_NO_TMP_DIR => 'Diretório temporário não disponível',
            UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever arquivo no disco',
            UPLOAD_ERR_EXTENSION  => 'Upload bloqueado pela extensão do PHP'
        ];
        
        return $mensagens[$codigoErro] ?? 'Erro desconhecido no upload';
    }
    
    /**
     * Formata tamanho em bytes para formato legível
     *
     * @param int $bytes
     * @return string
     */
    private function formatarTamanho(int $bytes): string
    {
        $unidades = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($unidades) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $unidades[$pow];
    }
}
