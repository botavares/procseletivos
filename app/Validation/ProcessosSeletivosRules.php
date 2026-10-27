<?php

declare(strict_types=1);

namespace App\Validation;

class ProcessosSeletivosRules
{
    /**
     * Regra personalizada: aceita apenas números.
     */
    public function apenas_numeros(?string $value = null, ?string $params = null, array $data = []): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        return ctype_digit($value);
    }

    /**
     * Regra personalizada: ano deve ser igual ou maior que o ano atual.
     */
    public function ano_atual_ou_futuro(?string $value = null, ?string $params = null, array $data = []): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        if (!ctype_digit($value)) {
            return false;
        }

        $anoInformado = (int) $value;
        $anoAtual     = (int) date('Y');

        return $anoInformado >= $anoAtual;
    }

    /**
     * Regra personalizada: data fim deve ser maior ou igual à data início.
     * Parâmetro: nome do campo que contém a data de início.
     */
    public function data_fim_maior_igual_inicio(?string $value = null, ?string $params = null, array $data = []): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        if (empty($params) || !isset($data[$params])) {
            return false;
        }

        $dataInicio = $data[$params];
        $dataFim    = $value;

        // Espera formato d/m/Y
        $partesInicio = explode('/', $dataInicio);
        $partesFim    = explode('/', $dataFim);

        if (count($partesInicio) !== 3 || count($partesFim) !== 3) {
            return false;
        }

        $tsInicio = strtotime("{$partesInicio[2]}-{$partesInicio[1]}-{$partesInicio[0]}");
        $tsFim    = strtotime("{$partesFim[2]}-{$partesFim[1]}-{$partesFim[0]}");

        if ($tsInicio === false || $tsFim === false) {
            return false;
        }

        return $tsFim >= $tsInicio;
    }
}
