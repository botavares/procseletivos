<?php
namespace App\Services\Candidatos;

use App\Models\CandidatosModel;
use App\Models\EditaisCandidatosModel;
use App\Models\EditaisModel;
use App\Models\EditaisCargosModel;
use App\Models\CargosModel;

class CandidatosService{
    protected $candidatosModel;
    protected $editaisCandidatosModel;
    protected $editaisModel;
    protected $editaisCargosModel;
    protected $cargosModel;

    public function __construct()
    {
        $this->candidatosModel = new CandidatosModel();
        $this->editaisCandidatosModel = new EditaisCandidatosModel();
        $this->editaisModel = new EditaisModel();
        $this->editaisCargosModel = new EditaisCargosModel();
        $this->cargosModel = new CargosModel();
    }


    public function listarCandidatos($idEdital, $idCargo){
        $this->editaisCandidatosModel->select('tb_cadastrados.*, tb_editais_candidatos.fk_id_edital, tb_editais_candidatos.fk_id_cargo');
        $this->editaisCandidatosModel->join('tb_cadastrados', 'tb_cadastrados.pk_id_cadastrado = tb_editais_candidatos.fk_id_candidato');
        $this->editaisCandidatosModel->where('tb_editais_candidatos.fk_id_edital', $idEdital);
        $this->editaisCandidatosModel->where('tb_editais_candidatos.fk_id_cargo', $idCargo);
        return $this->editaisCandidatosModel->findAll();
    }

    public function listarCandidatoId($edital,$cargo,$id){
        $candidato = $this->candidatosModel->listarCandidatoId($edital,$cargo,$id);
        $experiencias = $this->candidatosModel->listarExperienciaCandidato($edital,$cargo,$id);
        $escolaridades = $this->candidatosModel->listarEscolaridadeCandidato($edital,$cargo,$id);
        $aperfeicoamentos = $this->candidatosModel->listarAperfeicoamentoCandidato($edital,$cargo,$id);

        return [
                'idEdital' => $edital,
                'idCargo' => $cargo,
                'idCandidato' => $id,
                'candidato' => $candidato, 
                'experiencias' => $experiencias, 
                'escolaridades' => $escolaridades, 
                'aperfeicoamentos' => $aperfeicoamentos
            ];
        
    }
}