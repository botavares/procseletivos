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
