<?php
namespace App\Services\Admin\Candidatos;

use App\Repositories\Admin\Interfaces\ICandidatoRepository;
use Config\Database;

class CandidatosService{
    protected $candidatosRepository;

    public function __construct(
        ?ICandidatoRepository $candidatosRepository = null
    )
    {
        $this->candidatosRepository = $candidatosRepository ?? service('adminCandidatoRepository');
    }

    public function listarCandidatos($idEdital, $idCargo){
        return $this->candidatosRepository->buscarPorEditalCargo($idEdital, $idCargo);
    }

    public function listarCandidatoId($edital,$cargo,$id){
        $processo = $this->resolverProcessoPorEdital((int) $edital);

        $candidato = $this->candidatosRepository->buscarPorId($edital,$cargo,$id);
        $experiencias = $this->candidatosRepository->buscarExperiencias($processo ?? $edital,$cargo,$id);
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

    private function resolverProcessoPorEdital(int $idEdital): ?int
    {
        $db = Database::connect();

        $edital = $db->table('tb_editais')
            ->where('pk_id_edital', $idEdital)
            ->get()
            ->getRow();

        if (empty($edital->ds_numero_edital) || strlen((string) $edital->ds_numero_edital) < 5) {
            return null;
        }

        $numeroCompleto = (string) $edital->ds_numero_edital;
        $ano    = (int) substr($numeroCompleto, -4);
        $numero = (int) substr($numeroCompleto, 0, -4);

        $row = $db->table('tb_processos')
            ->select('pk_id_processo')
            ->where([
                'nm_numero_processo' => $numero,
                'nm_ano_processo'    => $ano,
            ])
            ->where("(is_deletado IS NULL OR is_deletado = '0000-00-00 00:00:00')", null, false)
            ->get()
            ->getRow();

        return $row ? (int) $row->pk_id_processo : null;
    }
}
