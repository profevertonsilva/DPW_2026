<?php

namespace FW\Controller;



class FuncoesGlobais
{



    function popularModel($model, $data)
    {
        foreach ($data as $key => $value) {
            // Chama o método mágico __set diretamente para atribuir o valor à propriedade
            $model->__set($key, $value);
        }
    }

    function criptografar($senha)
    {
        // Criptografa a senha usando o algoritmo MD5
        return hash('md5', $senha);
    }

    function converterData($data)
    {
        // Verifica se a data está no formato dd/mm/yyyy
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data)) {
            $partes = explode('/', $data); // Divide a string pelo delimitador "/"
            return $partes[2] . '-' . $partes[1] . '-' . $partes[0]; // Converte para formato yyyy-mm-dd
        }

        // Verifica se a data está no formato yyyy-mm-dd
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            $partes = explode('-', $data); // Divide a string pelo delimitador "-"
            return $partes[2] . '/' . $partes[1] . '/' . $partes[0]; // Converte para formato dd/mm/yyyy
        }

        // Retorna vazio se a data não estiver em um formato válido
        return '';
    }

    function limparTelefone($telefone)
    {
        // Remove todos os caracteres que não sejam números
        return preg_replace('/\D/', '', $telefone);
    }

    function formatarNumeroParaMoeda($numero)
    {
        // Converte o número para float e divide por 100
        $valorFormatado = number_format($numero, 2, ',', '.');
        return $valorFormatado;
    }

    function converterValorParaNumerico($valor)
    {
        // Remove os pontos do separador de milhar
        $valorSemPontos = str_replace('.', '', $valor);

        // Substitui a vírgula pelo ponto para o separador decimal
        $valorConvertido = str_replace(',', '.', $valorSemPontos);

        return $valorConvertido;
    }

    function formatarTelefone($numero)
    {
        // Verifica se o número tem o tamanho esperado (11 dígitos para celulares com DDD)
        if (strlen($numero) == 11) {
            $ddd = substr($numero, 0, 2); // Extrai os dois primeiros dígitos (DDD)
            $parte1 = substr($numero, 2, 5); // Extrai os próximos cinco dígitos
            $parte2 = substr($numero, 7, 4); // Extrai os últimos quatro dígitos
            return "($ddd) $parte1-$parte2";
        } elseif (strlen($numero) == 10) {
            // Para telefones fixos no formato com 10 dígitos
            $ddd = substr($numero, 0, 2);
            $parte1 = substr($numero, 2, 4);
            $parte2 = substr($numero, 6, 4);
            return "($ddd) $parte1-$parte2";
        } else {
            // Retorna o número original se não for possível formatar
            return $numero;
        }
    }

    public function limparCpf($cpf)
    {
        return str_replace(['.', '-'], '', trim($cpf));
    }

    public function cpfValido($cpf)
    {
        $cpf = $this->limparCpf($cpf);

        // Verifica se tem 11 dígitos
        if (strlen($cpf) !== 11) {
            return false;
        }

        // Verifica se contém apenas números
        if (!ctype_digit($cpf)) {
            return false;
        }

        // Elimina CPFs com todos os dígitos iguais: 00000000000, 11111111111 etc.
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Validação do primeiro dígito verificador
        $soma = 0;

        for ($i = 0; $i < 9; $i++) {
            $soma += intval($cpf[$i]) * (10 - $i);
        }

        $resto = $soma % 11;
        $digito1 = ($resto < 2) ? 0 : 11 - $resto;

        if ($digito1 != intval($cpf[9])) {
            return false;
        }

        // Validação do segundo dígito verificador
        $soma = 0;

        for ($i = 0; $i < 10; $i++) {
            $soma += intval($cpf[$i]) * (11 - $i);
        }

        $resto = $soma % 11;
        $digito2 = ($resto < 2) ? 0 : 11 - $resto;

        if ($digito2 != intval($cpf[10])) {
            return false;
        }

        return true;
    }
}
