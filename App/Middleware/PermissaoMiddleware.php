<?php

namespace App\Middleware;


class PermissaoMiddleware
{
    public const ADMIN = 1;
    public const MODERADOR = 2;
    public const ONG = 3;
    public const VETERINARIO = 4;
    public const ADOTANTE = 5;
    public const VISITANTE = 6;

    public static function obterNivelAtual()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        };

        if (empty($_SESSION['id'])) {
            return self::VISITANTE;
        }

        return (int) ($_SESSION['nivel_permissao'] ?? self::VISITANTE);
    }

    public static function exigirLogin()
    {
        if (self::obterNivelAtual() === self::VISITANTE) {
            header('Location: /?erro=autenticacao');
            die();
        }
    }


    public static function exigirNivel($nivelNecessario)
    {
        $nivelAtual = self::obterNivelAtual();

        if ($nivelAtual === self::VISITANTE) {
            header('Location: /?erro=autenticacao');
            die();
        }

        if ($nivelAtual > $nivelNecessario) {
            header('Location: /error403');
            die();
        }
    }
    public static function obterNivelPorTipo($tipo_usuario)
    {
        return match ($tipo_usuario) {
            'administrador' => self::ADMIN,
            'moderador' => self::MODERADOR,
            'veterinario' => self::VETERINARIO,
            'ong' => self::ONG,
            'rastreador' => self::ADOTANTE,
            'adotante' => self::ADOTANTE,
            default => self::VISITANTE
        };
    }
}
