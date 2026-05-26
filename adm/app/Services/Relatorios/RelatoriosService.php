<?php
namespace App\Services\Relatorios;

use App\Models\CandidatosModel;
use App\Models\AcademicosModel;
use App\Models\CursosModel;
use App\Models\AbrangenciasModel;

class RelatoriosService{
    public function __construct(
        private CandidatosModel $candidatosModel,
        private AcademicosModel $academicosModel,
        private CursosModel $cursosModel,
        private AbrangenciasModel $abrangenciasModel
    ){}

    public function relatorioCandidatosPorCurso(int $idCurso): array{
        $cursosModel            = new CursosModel();
        $dadosCurso = $cursosModel->where('pk_id_curso', $idCurso)->first();

        if (!$dadosCurso) {
            throw new \RuntimeException('Cursos não encontrados');
        }

        $candidatos = $this->candidatosModel
            ->select('tb_dados_pessoais.*, tb_dados_academicos.fk_id_curso,tb_cursos.ds_nome_curso, tb_dados_academicos.ds_periodo')
            ->join('tb_dados_academicos', 'tb_dados_academicos.pk_id_candidato = tb_dados_pessoais.pk_id_candidato')
            ->join('tb_cursos', 'tb_cursos.pk_id_curso = tb_dados_academicos.fk_id_curso')
            ->where('tb_dados_academicos.fk_id_curso', $idCurso)
            ->orderBy('tb_dados_academicos.ds_periodo', 'DESC')
            ->orderBy('tb_dados_pessoais.ds_nascimento', 'ASC')
            ->findAll();

        return [
            'brasao'            => imageToBase64(ROOTPATH . '/external/img/brasao.png'),
            'fundo'             => imageToBase64(ROOTPATH . '/external/img/fundo.jpg'),
            'curso'             => $dadosCurso->ds_nome_curso,
            'candidatos'        => $candidatos,
            'cursoSelecionado'  => $idCurso,
            'titulo'            => 'Candidatos Por Curso',
        ];
    }

    public function relatorioCandidatosPorAbrangencia(int $idAbrangencia): array{
        $abrangenciasModel            = new AbrangenciasModel();
        $dadosAbrangencia = $abrangenciasModel->where('pk_id_abrangencia', $idAbrangencia)->first();

        if (!$dadosAbrangencia) {
            throw new \RuntimeException('Abrangência não encontrada');
        }

        $candidatos = $this->candidatosModel
            ->select('tb_dados_pessoais.*, tb_dados_academicos.fk_id_curso,tb_cursos.ds_nome_curso, tb_dados_academicos.ds_periodo')
            ->join('tb_dados_academicos', 'tb_dados_academicos.pk_id_candidato = tb_dados_pessoais.pk_id_candidato')
            ->join('tb_cursos', 'tb_cursos.pk_id_curso = tb_dados_academicos.fk_id_curso')
            ->where('tb_cursos.fk_id_abrangencia', $idAbrangencia)
            ->orderBy('tb_cursos.ds_nome_curso', 'ASC')
            ->orderBy('tb_dados_academicos.ds_periodo', 'DESC')
            ->orderBy('tb_dados_pessoais.ds_nascimento', 'ASC')
            ->findAll();

        return [
            'brasao'                    => imageToBase64(ROOTPATH . '/external/img/brasao.png'),
            'fundo'                     => imageToBase64(ROOTPATH . '/external/img/fundo.jpg'),
            'abrangencia'               => $dadosAbrangencia->ds_nome_abrangencia,           
            'candidatos'                => $candidatos,
            'abrangenciaSelecionada'    => $idAbrangencia,
            'titulo'                    => 'Candidatos Por Abrangência',
        ];
    }

}
