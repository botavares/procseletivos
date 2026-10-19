<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IVinculoCandidatoService
{
    public function atualizarVinculoCurso(int $candidatoId, int $novoCursoId): void;

    public function registrarVinculoCurso(int $candidatoId, int $cursoId): void;
}
