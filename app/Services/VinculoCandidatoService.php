<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AcademicosModel;
use App\Models\Admin\CursosModel;
use App\Models\Admin\EditaisCandidatosModel;
use App\Services\Interfaces\IVinculoCandidatoService;

class VinculoCandidatoService implements IVinculoCandidatoService
{
    public function __construct(
        private AcademicosModel $academicos,
        private CursosModel $cursos,
        private EditaisCandidatosModel $editaisCandidatos,
    ) {}

    public function atualizarVinculoCurso(int $candidatoId, int $novoCursoId): void
    {
        // Funcionalidade legada de vínculo automático por curso foi descontinuada
        // com a mudança de edital para processo seletivo.
    }

    public function registrarVinculoCurso(int $candidatoId, int $cursoId): void
    {
        // Funcionalidade legada de vínculo automático por curso foi descontinuada.
    }
}
