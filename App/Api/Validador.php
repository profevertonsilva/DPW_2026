<?php

namespace App\Api;

/**
 * Validações de entrada da API. Sintaxe compatível com PHP 7.0.
 */
class Validador
{
    /** Valida formato de e-mail. */
    public static function email($email): bool
    {
        return is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Regra de senha (RF#01): mínimo 8 caracteres, com ao menos 1 caractere
     * alfanumérico e ao menos 1 caractere especial.
     */
    public static function senhaForte($senha): bool
    {
        if (!is_string($senha) || strlen($senha) < 8) {
            return false;
        }
        $temAlfanumerico = (bool) preg_match('/[A-Za-z0-9]/', $senha);
        $temEspecial     = (bool) preg_match('/[^A-Za-z0-9]/', $senha);

        return $temAlfanumerico && $temEspecial;
    }
}
