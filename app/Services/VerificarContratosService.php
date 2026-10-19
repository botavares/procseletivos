<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\VerificadorModel;
use App\Models\EditaisCandidatosModel;
use App\Models\DadosContratosModel;
use App\Models\CandidatosModel;
use App\Models\AcademicosModel;
use App\Services\Interfaces\IVerificarContratosService;

class VerificarContratosService implements IVerificarContratosService
{
    public function __construct(
        private VerificadorModel $verificadorModel,
        private DadosContratosModel $dadosContratoModel,
        private CandidatosModel $candidatosModel,
        private AcademicosModel $academicosModel,
    ) {}

    public function verificarContrato($codigoVerificacao)
    {
        $verificador = $this->verificadorModel->where('ds_verificador', $codigoVerificacao)->first();

        if (!$verificador) {
            return false;
        }

        $dadosContrato = $this->dadosContratoModel->where('pk_id_contrato', $verificador->pk_id_contrato)->first();

        if (!$dadosContrato) {
            return false;
        }

        $candidato = $this->candidatosModel->where('pk_id_candidato', $dadosContrato->fk_id_candidato)->first();

        return [
            'dataInicio' => $dadosContrato->ds_data_ingresso,
            'dataTermino' => $dadosContrato->ds_data_encerramento,
            'candidato' => $candidato->ds_nome ?? '',
            'verificador' => $verificador,
        ];
    }
}
