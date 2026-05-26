<?php

namespace App\Services;

use App\Models\AcademicosModel;
use App\Models\CursosModel;
use App\Models\EditaisCandidatosModel;
use App\Models\EditaisCursosModel;

class VinculoCandidatoService
{
    protected $academicos;
    protected $cursos;
    protected $editaisCandidatos;
    protected $editaisCursos;

    public function __construct()
    {
        $this->academicos        = new AcademicosModel();
        $this->cursos            = new CursosModel();
        $this->editaisCandidatos = new EditaisCandidatosModel();
        $this->editaisCursos     = new EditaisCursosModel();
    }

    /**
     * Atualiza o vínculo do candidato quando o curso é alterado
     */
    public function atualizarVinculoCurso(int $candidatoId, int $novoCursoId): void{
        $dadosAcademicos = $this->academicos->find($candidatoId);
        $cursoAtual      = $this->cursos->find($dadosAcademicos->fk_id_curso ?? 0);

        // Buscar edital do curso atual
        $editalAtual = $cursoAtual
            ? $this->editaisCursos->getEditaisAtivosCursos($cursoAtual->pk_id_curso)
            : null;

        // Buscar edital do novo curso
        $novoEdital = $this->editaisCursos->getEditaisAtivosCursos($novoCursoId);

        // Se curso mudou
        if ($novoCursoId != ($cursoAtual->pk_id_curso ?? null)) {
            
            // Verificar se existe vínculo com edital antigo
            $vinculoAtual = $this->editaisCandidatos->verificarVinculos(
                $candidatoId,
                $editalAtual->fk_id_edital ?? 0
            );

            // Se houver vínculo → remover
            if ($vinculoAtual && $editalAtual) {
                $this->editaisCandidatos
                    ->where('fk_id_candidato', $candidatoId)
                    ->where('fk_id_edital', $editalAtual->fk_id_edital)
                    ->delete();
            }

            // Se o novo curso tiver edital → criar vínculo
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
    public function registrarVinculoCurso(int $candidatoId, int $cursoId): void{
        $curso = $this->cursos->find($cursoId);
        if($curso){
            $edital = $this->editaisCursos->getEditaisAtivosCursos($curso->pk_id_curso);
            if($edital){
                $vinculoExistente = $this->editaisCandidatos->verificarVinculos($candidatoId, $edital->fk_id_edital);
                if(!$vinculoExistente){
                    $this->editaisCandidatos->insert([
                        'fk_id_edital'    => $edital->fk_id_edital,
                        'fk_id_candidato' => $candidatoId,
                        'fk_id_curso'     => $cursoId,
                        'ds_observacao'   => 'Vínculo registrado automaticamente em ' . date('d/m/Y'),
                        'ds_status'       => 1
                    ]);
                }
            }
        }
    }
}
