<?php
namespace App\Services\Candidatos;

use App\Repositories\Interfaces\ICandidatoRepository;

class CandidatosService{
    protected $candidatosRepository;

    public function __construct(
        ?ICandidatoRepository $candidatosRepository = null
    )
    {
        $this->candidatosRepository = $candidatosRepository ?? service('candidatoRepository');
    }

    public function listarCandidatos($idEdital, $idCargo){
        return $this->candidatosRepository->buscarPorEditalCargo($idEdital, $idCargo);
    }

    public function listarCandidatoId($edital,$cargo,$id){
        $candidato = $this->candidatosRepository->buscarPorId($edital,$cargo,$id);
        $experiencias = $this->candidatosRepository->buscarExperiencias($edital,$cargo,$id);
        $escolaridades = $this->candidatosRepository->buscarEscolaridades($edital,$cargo,$id);
        $aperfeicoamentos = $this->candidatosRepository->buscarAperfeicoamentos($edital,$cargo,$id);
        $criterios = $this->candidatosRepository->buscarCriterios($edital,$cargo,$id);

        return [
                'idEdital' => $edital,
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