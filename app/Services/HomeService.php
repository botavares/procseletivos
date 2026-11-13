<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CandidatosModel;
use App\Services\Base\SessaoService;

class HomeService
{
    public function __construct(
        private CandidatosModel $candidatosModel,
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

        return [
            'layout' => 'Publica/layoutLogado',
            'labelCaption' => 'Seus dados',
            'dataSession' => $usuario,
            'edital' => null,
            'status' => 'registrado',
        ];
    }
}
