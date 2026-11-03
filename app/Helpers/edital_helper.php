<?php

/**
 * Helpers de formatação de editais
 */

if (!function_exists('formatarNumeroEdital')) {
    /**
     * Formata o número do edital no padrão 001/2026
     *
     * Exemplo:
     * 12026  -> 001/2026
     * 152026 -> 015/2026
     * 1002026 -> 100/2026
     *
     * @param int|string $numeroEdital
     * @return string|null
     */
    function formatarNumeroEdital($numeroEdital): ?string
    {
        if (empty($numeroEdital)) {
            return null;
        }

        // Garante que estamos trabalhando com string
        $numeroEdital = (string) $numeroEdital;

        // Deve ter pelo menos 5 dígitos (1 número + 4 do ano)
        if (strlen($numeroEdital) < 5) {
            return null;
        }

        $ano = substr($numeroEdital, -4);
        $numero = substr($numeroEdital, 0, -4);

        // Formata com 3 dígitos (padding com zeros à esquerda)
        $numero = str_pad($numero, 3, '0', STR_PAD_LEFT);

        return $numero . '/' . $ano;
    }
}

if (!function_exists('pasta_tipo_documento')) {
    /**
     * Retorna o nome da pasta de uploads para um tipo de documento.
     */
    function pasta_tipo_documento(int $tipo): string
    {
        return match ($tipo) {
            1       => 'original',
            2       => 'retificacoes',
            3, 4, 5, 7 => 'republicacoes',
            6       => 'anexos',
            default => 'anexos',
        };
    }
}

if (!function_exists('url_diario_oficial')) {
    /**
     * Monta o link do Diário Oficial a partir do código oficial.
     */
    function url_diario_oficial(?string $codigo): string
    {
        if (empty($codigo)) {
            return '#';
        }
        return rtrim(DIARIO_OFICIAL_URL_BASE, '/') . '/' . rawurlencode($codigo);
    }
}
