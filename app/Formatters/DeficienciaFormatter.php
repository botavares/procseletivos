<?php

declare(strict_types=1);

namespace App\Formatters;

class DeficienciaFormatter
{
    /**
     * Retorna descrição da deficiência
     */
    public static function formatar(?int $idPne, ?string $outraPne): string
    {
        $deficiencias = [
            1 => 'não possui deficiência',
            2 => 'possui deficiência auditiva',
            3 => 'possui deficiência física',
            4 => 'possui deficiência múltipla',
            5 => 'possui deficiência mental',
            6 => 'possui deficiência reabilitado',
            7 => 'possui deficiência visual',
            8 => 'possui deficiência: ' . $outraPne,
        ];

        return $deficiencias[$idPne] ?? 'não informado';
    }
}
