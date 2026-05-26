<?php

namespace App\Traits;

trait FormatarNumeroEditalTrait
{
    /**
     * Formata o número do edital no padrão numero/ano
     *
     * Exemplo:
     * 12026 -> 1/2026
     * 152026 -> 15/2026
     *
     * @param int|string $numeroEdital
     * @return string|null
     */
    public function formatarNumeroEdital($numeroEdital): ?string
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

        // Remove zeros à esquerda (caso existam)
        $numero = ltrim($numero, '0');

        return $numero . '/' . $ano;
    }
}