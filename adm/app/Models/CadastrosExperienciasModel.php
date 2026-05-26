<?php
namespace App\Models;

use CodeIgniter\Model;

class CadastrosExperienciasModel extends Model{
    //Atributos
    protected $table = 'tb_cadastrados_experiencias';
    protected $primaryKey = 'pk_id_experiencias';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'fk_id_edital',
        'fk_id_cargo',
        'fk_id_experiencia',
        'ds_status',
        'ds_quantidade',
        'ds_multiplicador',
    ];

    protected $returnType = 'object';

   public function buscarExperienciasParaClassificacao($cadastrado, $edital, $cargo)
{
    return $this->select([
        'tb_cadastrados_experiencias.fk_id_cadastrado',
        'tb_cadastrados_experiencias.fk_id_edital',
        'tb_cadastrados_experiencias.fk_id_experiencia',
        'tb_cadastrados_experiencias.ds_status',
        'tb_cadastrados_experiencias.ds_quantidade',
        'tb_cadastrados_experiencias.ds_multiplicador',
        'tb_experiencias.ds_nome_experiencia',
        'tb_cargos_experiencias_editais.ds_desempate',
        'tb_cargos_experiencias_editais.ds_quantidade_maxima'
    ])
    ->join(
        'tb_cargos_experiencias_editais',
        'tb_cadastrados_experiencias.fk_id_cargo = tb_cargos_experiencias_editais.fk_id_cargo
         AND tb_cadastrados_experiencias.fk_id_experiencia = tb_cargos_experiencias_editais.fk_id_experiencia'
    )
    ->join(
        'tb_experiencias',
        'tb_cadastrados_experiencias.fk_id_experiencia = tb_experiencias.pk_id_experiencia'
    )
    ->where([
        'tb_cadastrados_experiencias.fk_id_edital'     => $edital,
        'tb_cadastrados_experiencias.fk_id_cargo'      => $cargo,
        'tb_cadastrados_experiencias.fk_id_cadastrado' => $cadastrado,
    ])
    ->findAll();
}
public function listarExperiencias($idCargo){
    return $this->db->table('tb_cargos_experiencias_editais exp')
    ->select("
        exp.fk_id_experiencia,
        e.ds_nome_experiencia,
        exp.ds_multiplicador,
        exp.ds_quantidade_minima,
        exp.ds_quantidade_maxima,
        exp.ds_tipo_campo,
    ")
    ->join(
        'tb_experiencias e',
        'e.pk_id_experiencia = exp.fk_id_experiencia'
    )
    ->where('exp.fk_id_cargo', $idCargo)
    ->get()
    ->getResult();
}

}