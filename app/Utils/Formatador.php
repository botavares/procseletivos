<?php
namespace App\Utils;

class Formatador
{
    /**
     * Remove todos os caracteres não numéricos
     */
    public static function limparCpf(string $cpf): string
    {
        return preg_replace('/[^0-9]/', '', $cpf);
    }

    public static function limparCep(?string $cep): ?string
    {
        if (empty($cep)) return null;
        return preg_replace('/[^0-9]/', '', $cep);
    }

    public static function limparTelefone(?string $telefone): ?string
    {
        if (empty($telefone)) return null;
        return preg_replace('/[^0-9]/', '', $telefone);
    }

    /**
     * Converte data do formato brasileiro (dd/mm/YYYY) para ISO (YYYY-mm-dd)
     * Retorna null se o formato for inválido
     */
    public static function formatarDataBrParaIso(?string $data): ?string
    {
        if (empty($data)) return null;

        $partes = explode('/', $data);
        if (count($partes) !== 3) return null;

        list($dia, $mes, $ano) = $partes;

        // Validações básicas
        if (!is_numeric($dia) || !is_numeric($mes) || !is_numeric($ano)) return null;
        if (strlen($ano) !== 4) return null;
        if (!checkdate((int) $mes, (int) $dia, (int) $ano)) return null;

        return sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
    }
}
