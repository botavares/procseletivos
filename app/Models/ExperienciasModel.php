<?php
namespace App\Models;

use CodeIgniter\Model;

class ExperienciasModel extends Model{
    //Atributos
    protected $table = 'tb_experiencias';
    //colocar chave compost primary key:
    
    protected $primaryKey = 'pk_id_experiencia';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'ds_nome_experiencia','ds_tipo_experiencia'
    ];

    protected $returnType = 'object';

    public function listarRequisitosExperiencias($cargo = null){
        return $this->db->table('tb_cargos_experiencias')
        ->select([
            
            'tb_cargos_experiencias.fk_id_cargo',
            'tb_cargos_experiencias.fk_id_experiencia',
            'tb_experiencias.ds_nome_experiencia',
            'tb_cargos_experiencias.ds_tipo_campo',
            'tb_cargos.ds_nome_cargo',
            'tb_cargos_experiencias.ds_pontuacao_minima',
            'tb_cargos_experiencias.ds_pontuacao_maxima',
            'tb_experiencias.ds_tipo_experiencia',
            
            
        ])
        ->join('tb_experiencias', 'tb_experiencias.pk_id_experiencia = tb_cargos_experiencias.fk_id_experiencia')
        ->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_cargos_experiencias.fk_id_cargo')
        ->where('tb_cargos.pk_id_cargo', $cargo)
        ->orderBy('tb_cargos_experiencias.ds_tipo_campo', 'ASC')
        ->orderBy('tb_experiencias.ds_tipo_experiencia', 'ASC')
        ->get()
        ->getResult();
}

}