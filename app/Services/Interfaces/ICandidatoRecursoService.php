<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface ICandidatoRecursoService
{
    public function buscarResumoRecursosPorCandidato(int $candidatoId): array;

    public function buscarDetalhesRecurso(string $protocolo): array;
}
