<?php

namespace App\Services;

use App\Models\AcademicosModel;
use App\Models\CursosModel;
use App\Models\CandidatosModel;
use App\Models\EditaisCandidatosModel;
use App\Models\EditaisCursosModel;
use App\Models\DadosContratosModel;
use App\Models\SetoresModel;
use App\Models\FeriasModel;


class DadosContratadoService{
    protected $academicos;
    protected $cursos;
    protected $setores;
    protected $editaisCandidatos;
    protected $editaisCursos;
    protected $dadosContratado;

    public function __construct() {
        $this->academicos        = new AcademicosModel();
        $this->cursos            = new CursosModel();
        $this->editaisCandidatos = new EditaisCandidatosModel();
        $this->editaisCursos     = new EditaisCursosModel();
        $this->setores           = new SetoresModel();
        $this->dadosContratado   = new DadosContratosModel();
    }

    public function getDadosContratado($idCandidato) {
        $dadosContratado = $this->dadosContratado->getContratosPorId($idCandidato);
        return $dadosContratado;
    }

    public function contagemDeFerias($contratado,$setor){
        $contratosModel    = new DadosContratosModel();
        $candidatosModel   = new CandidatosModel();
        $setoresModel      = new SetoresModel();
        $feriasModel       = new FeriasModel();
        

        $dadosContratado   = $candidatosModel->where('pk_id_candidato', $contratado)->first();
        $dadosSetor        = $setoresModel->where('pk_id_setor', $setor)->first();
        $dadosContrato     = $contratosModel->where('fk_id_candidato', $contratado)
                                            ->where('fk_id_setor', $setor)
                                            ->first();
        $dataInicioContrato = date('d/m/Y', strtotime($dadosContrato->ds_data_ingresso));
        $dataEncerramento   = date('d/m/Y', strtotime($dadosContrato->ds_data_encerramento));
        //total de dias trabalhados
        $data1 = new \DateTime($dadosContrato->ds_data_ingresso);
        $data2 = new \DateTime(date('Y-m-d'));
        $interval = $data1->diff($data2);
        $diasTrabalhados = $interval->format('%a');//formato em dias
        //dias de férias arredondar diasTrabalhados / 12
        $diasFerias = floor($diasTrabalhados / 12);

        $dadosFerias = $feriasModel->where('fk_id_estagiario', $dadosContrato->fk_id_candidato)
                                    ->where('ds_ano_referente',$dadosContrato->ds_ano_termo)
                                    ->where('ds_status', 1)
                                    ->first();
                                    
        if($dadosFerias){
            $feriasTiradas = $dadosFerias->ds_dias_ferias;
            $diasFerias = $diasFerias - $feriasTiradas;
        }else{
            $feriasTiradas = 0;
        }

        $dadosFerias = array(
            'diasTrabalhados' => $diasTrabalhados,
            'feriasTiradas' => $feriasTiradas,
            'diasFerias'    => $diasFerias,
        );

        return $dadosFerias;
    }
    

}