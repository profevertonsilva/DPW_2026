<?php

namespace App\Api;

/**
 * Bloqueio progressivo de login (RF#02): após 3 falhas, bloqueia ~10s,
 * dobrando a cada nova falha (10→20→40…). Chave por e-mail + IP.
 *
 * Armazenamento em arquivo (storage/throttle/), evitando alterar o schema
 * compartilhado. Sintaxe compatível com PHP 7.0.
 */
class LoginThrottle
{
    /** Falhas toleradas antes de começar a bloquear. */
    const LIMITE_LIVRE = 3;

    /** Tempo base de bloqueio, em segundos. */
    const BASE_SEGUNDOS = 10;

    /** @var string */
    private $dir;

    public function __construct()
    {
        $this->dir = dirname(__DIR__, 2) . '/storage/throttle';
        if (!is_dir($this->dir)) {
            @mkdir($this->dir, 0775, true);
        }
    }

    /**
     * Lança ApiException 429 se o par e-mail/IP estiver bloqueado no momento.
     */
    public function verificar($email, $ip)
    {
        $estado   = $this->ler($this->arquivo($email, $ip));
        $restante = $estado['bloqueado_ate'] - time();
        if ($restante > 0) {
            throw new ApiException(
                "Muitas tentativas. Tente novamente em {$restante} segundo(s).",
                429
            );
        }
    }

    /** Registra uma tentativa falha e aplica o bloqueio progressivo. */
    public function registrarFalha($email, $ip)
    {
        $arquivo = $this->arquivo($email, $ip);
        $estado  = $this->ler($arquivo);
        $estado['falhas']++;

        if ($estado['falhas'] >= self::LIMITE_LIVRE) {
            $expoente = $estado['falhas'] - self::LIMITE_LIVRE; // 0, 1, 2…
            $segundos = self::BASE_SEGUNDOS * (2 ** $expoente);
            $estado['bloqueado_ate'] = time() + $segundos;
        }

        @file_put_contents($arquivo, json_encode($estado), LOCK_EX);
    }

    /** Limpa o histórico de falhas após um login bem-sucedido. */
    public function registrarSucesso($email, $ip)
    {
        $arquivo = $this->arquivo($email, $ip);
        if (is_file($arquivo)) {
            @unlink($arquivo);
        }
    }

    private function arquivo($email, $ip): string
    {
        $chave = sha1(strtolower(trim($email)) . '|' . $ip);
        return $this->dir . '/' . $chave . '.json';
    }

    private function ler($arquivo): array
    {
        $padrao = ['falhas' => 0, 'bloqueado_ate' => 0];
        if (!is_file($arquivo)) {
            return $padrao;
        }
        $dados = json_decode((string) file_get_contents($arquivo), true);
        if (!is_array($dados)) {
            return $padrao;
        }
        return array_merge($padrao, $dados);
    }
}
