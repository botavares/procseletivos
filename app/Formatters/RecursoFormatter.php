<?php

declare(strict_types=1);

namespace App\Formatters;

class RecursoFormatter
{
    /**
     * Formata o número do edital para exibição
     */
    public static function formatarNumeroEdital(string $dsNumeroEdital): string
    {
        if (empty($dsNumeroEdital)) {
            return '';
        }

        $ano = substr($dsNumeroEdital, -4);
        $numero = substr($dsNumeroEdital, 0, -4);
        $numero = ltrim($numero, '0');

        return $numero . '/' . $ano;
    }

    /**
     * Mapeia o tipo de status para código numérico
     */
    public static function mapearStatus(?string $tipo): int
    {
        if ($tipo === 'indeferido') {
            return 0;
        }

        if (in_array($tipo, ['alterado', 'inserido', 'removido'])) {
            return 1;
        }

        return 2;
    }

    /**
     * Retorna o label legível do status
     */
    public static function statusLabel(int $status): string
    {
        return match ($status) {
            0 => 'Indeferido',
            1 => 'Deferido',
            2 => 'Aguardando análise',
            default => 'Desconhecido',
        };
    }

    /**
     * Formata o valor de acordo com a categoria e tipo de campo
     */
    public static function formatarValor(string $categoria, string $tipoCampo, $valor): string
    {
        $v = $valor === null || $valor === '' ? null : (int) $valor;

        if ($tipoCampo === 'CHECK') {
            if ($v === null || $v === 0) {
                return 'Não';
            }

            return 'Sim';
        }

        // INPUT
        if ($categoria === 'experiencias') {
            if ($v === null) {
                return 'Não informado';
            }

            return $v . ' ano' . ($v > 1 ? 's' : '') . ' de experiência';
        }

        if ($v === null) {
            return 'Não informado';
        }

        return (string) $v;
    }
}
