<?php

namespace App\Api;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

/**
 * Emissão e validação de tokens JWT (HS256), assinados com JWT_SECRET (.env).
 * Expiração padrão de 7 dias, conforme a especificação da API (sem refresh token).
 */
class Jwt
{
    const ALGORITMO = 'HS256';
    const TTL_PADRAO = 604800; // 7 dias em segundos

    private static function segredo(): string
    {
        $segredo = $_ENV['JWT_SECRET'] ?? '';
        if ($segredo === '') {
            throw new ApiException('JWT_SECRET não configurado no servidor.', 500);
        }
        return $segredo;
    }

    /**
     * Gera um token a partir das claims informadas (ex.: sub, email, tipo_usuario, status).
     * iat e exp são preenchidos automaticamente.
     */
    public static function emitir(array $claims, int $ttlSegundos = self::TTL_PADRAO): string
    {
        $agora = time();
        $payload = array_merge($claims, [
            'iat' => $agora,
            'exp' => $agora + $ttlSegundos,
        ]);

        return FirebaseJWT::encode($payload, self::segredo(), self::ALGORITMO);
    }

    /**
     * Valida e decodifica o token. Lança exceção (Firebase) se inválido/expirado.
     *
     * @return object payload do JWT
     */
    public static function validar(string $token)
    {
        return FirebaseJWT::decode($token, new Key(self::segredo(), self::ALGORITMO));
    }
}
