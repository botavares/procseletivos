<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface IConvocadoRepository
{
    /**
     * Busca convocados por edital e curso (múltiplos JOINs).
     * @return array
     */
    public function buscarConvocadosPorEditalCurso(int $idEdital, int $idCurso): array;

    /**
     * Busca todos os convocados ativos.
     * @return array
     */
    public function buscarTodosConvocados(): array;
}
