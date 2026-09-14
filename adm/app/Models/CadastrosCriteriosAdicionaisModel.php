<?php
namespace App\Models;

use CodeIgniter\Model;

class CadastrosCriteriosAdicionaisModel extends Model{
    //Atributos
    protected $table = 'tb_cadastrados_criterios';
    protected $primaryKey = 'pk_id_criterios';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'fk_id_cadastrado', 
        'fk_id_edital',
        'fk_id_cargo',
        'fk_id_criterio',
        'ds_quantidade',
        'ds_multiplicador',
    ];

    protected $returnType = 'object';

    public function buscarCriteriosParaClassificacao($cadastrado, $edital, $cargo)
    {
        return $this->select([
            'tb_cadastrados_criterios.fk_id_cadastrado',
            'tb_cadastrados_criterios.fk_id_edital',
            'tb_cadastrados_criterios.fk_id_criterio',
            'tb_cadastrados_criterios.ds_quantidade',
            'tb_cadastrados_criterios.ds_multiplicador',
            'tb_criterios_adicionais.ds_nome_criterio',
            'tb_cargos_criterios_adicionais.ds_pontuacao_maxima'
        ])
        ->join(
            'tb_cargos_criterios_adicionais',
            'tb_cadastrados_criterios.fk_id_cargo = tb_cargos_criterios_adicionais.fk_id_cargo
             AND tb_cadastrados_criterios.fk_id_criterio = tb_cargos_criterios_adicionais.fk_id_criterio'
        )
        ->join(
            'tb_criterios_adicionais',
            'tb_cargos_criterios_adicionais.fk_id_criterio = tb_criterios_adicionais.pk_id_criterio'
        )
        ->where([
            'tb_cadastrados_criterios.fk_id_edital'     => $edital,
            'tb_cadastrados_criterios.fk_id_cargo'      => $cargo,
            'tb_cadastrados_criterios.fk_id_cadastrado' => $cadastrado,
        ])
        ->findAll();
    }

    public function listarCriterios($idCargo){
        return $this->db->table('tb_cargos_criterios_adicionais crit')
        ->select("
            crit.fk_id_criterio,
            c.ds_nome_criterio,
            crit.ds_pontuacao_minima,
            crit.ds_pontuacao_maxima,
            crit.ds_tipo_campo,
        ")
        ->join(
            'tb_criterios_adicionais c',
            'c.pk_id_criterio = crit.fk_id_criterio'
        )
        ->where('crit.fk_id_cargo', $idCargo)
        ->get()
        ->getResult();
    }
}
