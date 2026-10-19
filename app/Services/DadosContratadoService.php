<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CandidatosModel;
use App\Models\SetoresModel;
use App\Models\FeriasModel;
use App\Models\DadosContratosModel;
use App\Repositories\Interfaces\IContratoRepository;
use App\Services\Interfaces\IDadosContratadoService;

class DadosContratadoService implements IDadosContratadoService
{
    public function __construct(
        private CandidatosModel $candidatosModel,
        private SetoresModel $setoresModel,
        private FeriasModel $feriasModel,
        private DadosContratosModel $dadosContratosModel,
        private IContratoRepository $contratoRepository,
    ) {}

    public function getDadosContratado($idCandidato)
    {
        return $this->contratoRepository->buscarContratoCompletoPorCandidato((int) $idCandidato);
    }

    public function contagemDeFerias($contratado, $setor)
    {
        $dadosContratado   = $this->candidatosModel->where('pk_id_candidato', $contratado)->first();
        $dadosSetor        = $this->setoresModel->where('pk_id_setor', $setor)->first();
        $dadosContrato     = $this->dadosContratosModel->where('fk_id_candidato', $contratado)
            ->where('fk_id_setor', $setor)
            ->first();

        $dataInicioContrato = date('d/m/Y', strtotime($dadosContrato->ds_data_ingresso));
        $dataEncerramento   = date('d/m/Y', strtotime($dadosContrato->ds_data_encerramento));
        $data1 = new \DateTime($dadosContrato->ds_data_ingresso);
        $data2 = new \DateTime(date('Y-m-d'));
        $interval = $data1->diff($data2);
        $diasTrabalhados = $interval->format('%a');
        $diasFerias = floor($diasTrabalhados / 12);

        $dadosFerias = $this->feriasModel->where('fk_id_estagiario', $dadosContrato->fk_id_candidato)
            ->where('ds_ano_referente', $dadosContrato->ds_ano_termo)
            ->where('ds_status', 1)
            ->first();

        if ($dadosFerias) {
            $feriasTiradas = $dadosFerias->ds_dias_ferias;
            $diasFerias = $diasFerias - $feriasTiradas;
        } else {
            $feriasTiradas = 0;
        }

        return [
            'diasTrabalhados' => $diasTrabalhados,
            'feriasTiradas' => $feriasTiradas,
            'diasFerias' => $diasFerias,
        ];
    }
}
