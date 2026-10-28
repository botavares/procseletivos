<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CandidatosModel;
use App\Models\EditaisModel;
use App\Models\EditaisCandidatosModel;
use App\Services\Base\SessaoService;

class HomeService
{
    public function __construct(
        private CandidatosModel $candidatosModel,
        private EditaisModel $editaisModel,
        private EditaisCandidatosModel $editaisCandidatosModel,
        private SessaoService $sessaoService,
    ) {}

    public function obterDadosHome(): array
    {
        if (!$this->sessaoService->estaLogado()) {
            return [
                'layout' => 'Publica/layoutSimples',
                'labelCaption' => 'Cadastrar',
                'dataSession' => null,
                'edital' => null,
                'status' => 'naoregistrado',
            ];
        }

        $usuario = $this->sessaoService->obterUsuario();
        $dadosCandidatos = $this->candidatosModel->where('pk_id_cadastrado', $usuario['id'])->first();

        if (!$dadosCandidatos) {
            return [
                'layout' => 'Publica/layoutLogado',
                'labelCaption' => 'Seus dados',
                'dataSession' => $usuario,
                'edital' => null,
                'status' => 'naoregistrado',
            ];
        }

        $idCurso = $dadosCandidatos->fk_id_curso;
        $editaisCursos = $this->editaisModel->editaisPorCurso($idCurso);

        if (!$editaisCursos) {
            return [
                'layout' => 'Publica/layoutLogado',
                'labelCaption' => 'Seus dados',
                'dataSession' => $usuario,
                'edital' => null,
                'status' => 'naovinculado',
            ];
        }

        $verificaVinculo = $this->editaisCandidatosModel->verificarVinculos(
            $usuario['id'],
            $editaisCursos->fk_id_edital
        );

        if (!$verificaVinculo) {
            return [
                'layout' => 'Publica/layoutLogado',
                'labelCaption' => 'Seus dados',
                'dataSession' => $usuario,
                'edital' => $editaisCursos->fk_id_edital,
                'status' => 'naovinculado',
            ];
        }

        return [
            'layout' => 'Publica/layoutLogado',
            'labelCaption' => 'Seus dados',
            'dataSession' => $usuario,
            'edital' => formatarNumeroEdital($editaisCursos->ds_numero_edital),
            'status' => 'vinculado',
        ];
    }
}
