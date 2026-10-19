<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AcademicosModel;
use App\Models\CursosModel;
use App\Models\EditaisCandidatosModel;
use App\Models\EditaisCursosModel;
use App\Services\Interfaces\IVinculoCandidatoService;

class VinculoCandidatoService implements IVinculoCandidatoService
{
    public function __construct(
        private AcademicosModel $academicos,
        private CursosModel $cursos,
        private EditaisCandidatosModel $editaisCandidatos,
        private EditaisCursosModel $editaisCursos,
    ) {}

    public function atualizarVinculoCurso(int $candidatoId, int $novoCursoId): void
    {
        $dadosAcademicos = $this->academicos->find($candidatoId);
        $cursoAtual      = $this->cursos->find($dadosAcademicos->fk_id_curso ?? 0);

        $editalAtual = $cursoAtual
            ? $this->editaisCursos->getEditaisAtivosCursos($cursoAtual->pk_id_curso)
            : null;

        $novoEdital = $this->editaisCursos->getEditaisAtivosCursos($novoCursoId);

        if ($novoCursoId != ($cursoAtual->pk_id_curso ?? null)) {
            $vinculoAtual = $this->editaisCandidatos->verificarVinculos(
                $candidatoId,
                $editalAtual->fk_id_edital ?? 0
            );

            if ($vinculoAtual && $editalAtual) {
                $this->editaisCandidatos
                    ->where('fk_id_candidato', $candidatoId)
                    ->where('fk_id_edital', $editalAtual->fk_id_edital)
                    ->delete();
            }

            if ($novoEdital) {
                $this->editaisCandidatos->insert([
                    'fk_id_edital'    => $novoEdital->fk_id_edital,
                    'fk_id_candidato' => $candidatoId,
                    'fk_id_curso'     => $novoCursoId,
                    'ds_observacao'   => 'Vínculo atualizado automaticamente em ' . date('d/m/Y'),
                ]);
            }
        }
    }

    public function registrarVinculoCurso(int $candidatoId, int $cursoId): void
    {
        $curso = $this->cursos->find($cursoId);
        if ($curso) {
            $edital = $this->editaisCursos->getEditaisAtivosCursos($curso->pk_id_curso);
            if ($edital) {
                $vinculoExistente = $this->editaisCandidatos->verificarVinculos($candidatoId, $edital->fk_id_edital);
                if (!$vinculoExistente) {
                    $this->editaisCandidatos->insert([
                        'fk_id_edital'    => $edital->fk_id_edital,
                        'fk_id_candidato' => $candidatoId,
                        'fk_id_curso'     => $cursoId,
                        'ds_observacao'   => 'Vínculo registrado automaticamente em ' . date('d/m/Y'),
                        'ds_status'       => 1,
                    ]);
                }
            }
        }
    }
}
