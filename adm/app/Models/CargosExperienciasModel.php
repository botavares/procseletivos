<?php
namespace App\Models;

use CodeIgniter\Model;

class CargosExperienciasModel extends Model{
    //Atributos
    protected $table = 'tb_cargos_experiencias_editais';
    protected $primaryKey = 'pk_id_cargos_experiencias';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'fk_id_cargo',
        'fk_id_experiencia',
        'ds_obrigatório',
        'ds_quantidade_minima',
        'ds_quantidade_maxima',
        'ds_multiplicador',
        'ds_tipo_campo',
        'ds_desempate',
    ];
     protected $validationRules = [
        'fk_id_cargo' => 'required',
        'fk_id_experiencia' => 'required',
        'ds_quantidade_minima' => 'required',
        'ds_quantidade_maxima' => 'required',
        'ds_multiplicador' => 'required',
        'ds_tipo_campo' => 'required',
    ];

    public function listarExperienciasDoCargo($idCargo){
        return $this->db->table('tb_cargos_experiencias_editais')
            ->select([
                'tb_cargos_experiencias_editais.*',
                'tb_experiencias.ds_nome_experiencia',
            ])
            ->join('tb_experiencias', 'tb_experiencias.pk_id_experiencia = tb_cargos_experiencias_editais.fk_id_experiencia')
            ->where('tb_cargos_experiencias_editais.fk_id_cargo', $idCargo)
            ->orderBy('tb_experiencias.ds_nome_experiencia', 'ASC')
            ->get()
            ->getResult();
    }

}