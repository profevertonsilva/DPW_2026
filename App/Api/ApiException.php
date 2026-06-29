<?php

namespace App\Api;

/**
 * Exceção de fluxo da API: carrega a mensagem (em português, exibível ao
 * cliente) e o status HTTP que deve ser devolvido. Capturada pelo ApiRouter,
 * que a converte no padrão de erro { "erro": "..." }.
 */
class ApiException extends \Exception
{
    public function __construct(string $mensagem, int $statusHttp = 400)
    {
        parent::__construct($mensagem, $statusHttp);
    }

    public function status(): int
    {
        $codigo = (int) $this->getCode();
        return $codigo >= 400 ? $codigo : 400;
    }
}
