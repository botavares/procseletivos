<?php
namespace App\Services\Admin\Candidatos;

use App\Repositories\Admin\Interfaces\ICandidatoRepository;

class CandidatosService{
    protected $candidatosRepository;

    public function __construct(
        ?ICandidatoRepository $candidatosRepository = null
    )
    {
        $this->candidatosRepository = $candidatosRepository ?? service('adminCandidatoRepository');
    }

    public function listarCandidatos($idProcesso, $idCargo){
        return $this->candidatosRepository->buscarPorProcessoCargo($idProcesso, $idCargo);
    }

    public function listarCandidatoId($processo,$cargo,$id){
        $candidato = $this->candidatosRepository->buscarPorId($processo,$cargo,$id);
        $experiencias = $this->candidatosRepository->buscarExperiencias($processo,$cargo,$id);
        $escolaridades = $this->candidatosRepository->buscarEscolaridades($processo,$cargo,$id);
        $aperfeicoamentos = $this->candidatosRepository->buscarAperfeicoamentos($processo,$cargo,$id);
        $criterios = $this->candidatosRepository->buscarCriterios($processo,$cargo,$id);

        return [
                'idProcesso' => $processo,
                'idCargo' => $cargo,
                'idCandidato' => $id,
                'candidato' => $candidato,
                'experiencias' => $experiencias,
                'escolaridades' => $escolaridades,
                'aperfeicoamentos' => $aperfeicoamentos,
                'criterios' => $criterios
            ];

    }
}
