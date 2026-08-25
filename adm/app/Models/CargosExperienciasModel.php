<?php
namespace App\Models;

use CodeIgniter\Model;

class CargosExperienciasModel extends Model{
    //Atributos
    protected $table = 'tb_cargos_experiencias_editais';
    protected $primaryKey = 'pk_id_cargos_experiencia';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'fk_id_cargo',
        'fk_id_experiencia',
        'ds_obrigatorio',
        'ds_quantidade_minima',
        'ds_quantidade_maxima',
        'ds_multiplicador',
        'ds_tipo_campo',
        'ds_desempate',
        'pk_id_cargos_experiencia',
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
                'tb_cargos_experiencias_editais.pk_id_cargos_experiencia',
                'tb_cargos_experiencias_editais.fk_id_cargo',
                'tb_cargos_experiencias_editais.fk_id_experiencia',
                'tb_cargos_experiencias_editais.ds_obrigatorio',
                'tb_cargos_experiencias_editais.ds_quantidade_minima',
                'tb_cargos_experiencias_editais.ds_quantidade_maxima',
                'tb_cargos_experiencias_editais.ds_multiplicador',
                'tb_cargos_experiencias_editais.ds_tipo_campo',
                'tb_cargos_experiencias_editais.ds_desempate',
                'tb_experiencias.ds_nome_experiencia',
            ])
            ->join('tb_experiencias', 'tb_experiencias.pk_id_experiencia = tb_cargos_experiencias_editais.fk_id_experiencia')
            ->where('tb_cargos_experiencias_editais.fk_id_cargo', $idCargo)
            ->orderBy('tb_experiencias.ds_nome_experiencia', 'ASC')
            ->get()
            ->getResult();
    }

}