<?php

namespace App\Services;


use App\Models\VerificadorModel;
use App\Models\EditaisCandidatosModel;
use App\Models\DadosContratosModel;
use App\Models\CandidatosModel;
use App\Models\AcademicosModel;

class VerificarContratosService{
    public function verificarContrato($codigoVerificacao){
        $verificadorModel = new VerificadorModel();
        $verificador = $verificadorModel->where('ds_verificador', $codigoVerificacao)->first();
        if(!$verificador){
            return false;
        }else{
            $dadosContratoModel = new DadosContratosModel();
            $dadosContrato = $dadosContratoModel->where('pk_id_contrato', $verificador->pk_id_contrato)->first();
            if($dadosContrato){
               $candidatosModel = new CandidatosModel();
                $candidato = $candidatosModel->where('pk_id_candidato', $dadosContrato->fk_id_candidato)->first();
                $academicosModel = new AcademicosModel();
                $academico = $academicosModel->where('pk_id_candidato', $candidato->pk_id_candidato)->first();

                $arrayVerificador = [
                    'dataInicio' => $dadosContrato->ds_data_ingresso,
                    'dataTermino' => $dadosContrato->ds_data_encerramento,
                    'candidato' => $candidato->ds_nome,
                    'verificador' => $verificador,
                ];
            }else{
                return false;
            }
           
        }
        return $arrayVerificador;
    }
}