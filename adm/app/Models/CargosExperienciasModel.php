<?php
namespace App\Models;

use CodeIgniter\Model;

class CargosExperienciasModel extends Model{
    //Atributos
    protected $table = 'tb_cargos_experiencias';
    protected $primaryKey = 'pk_id_cargos_experiencia';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'fk_id_cargo',
        'fk_id_experiencia',
        'ds_pontuacao_minima',
        'ds_pontuacao_maxima',
        'ds_tipo_campo',
    ];
     protected $validationRules = [
        'fk_id_cargo' => 'required',
        'fk_id_experiencia' => 'required',
        'ds_pontuacao_minima' => 'required',
        'ds_pontuacao_maxima' => 'required',
        'ds_tipo_campo' => 'required',
    ];

    public function listarExperienciasDoCargo($idCargo){
        return $this->db->table('tb_cargos_experiencias')
            ->select([
                'tb_cargos_experiencias.pk_id_cargos_experiencia',
                'tb_cargos_experiencias.fk_id_cargo',
                'tb_cargos_experiencias.fk_id_experiencia',
                'tb_cargos_experiencias.ds_pontuacao_minima',
                'tb_cargos_experiencias.ds_pontuacao_maxima',
                'tb_cargos_experiencias.ds_tipo_campo',
                'tb_experiencias.ds_nome_experiencia',
            ])
            ->join('tb_experiencias', 'tb_experiencias.pk_id_experiencia = tb_cargos_experiencias.fk_id_experiencia')
            ->where('tb_cargos_experiencias.fk_id_cargo', $idCargo)
            ->orderBy('tb_experiencias.ds_nome_experiencia', 'ASC')
            ->get()
            ->getResult();
    }

}