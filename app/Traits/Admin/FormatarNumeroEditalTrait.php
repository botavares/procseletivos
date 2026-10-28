<?php

namespace App\Traits\Admin;

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
        return \formatarNumeroEdital($numeroEdital);
    }
}