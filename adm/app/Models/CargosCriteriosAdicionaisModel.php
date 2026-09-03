<?php
namespace App\Models;

use CodeIgniter\Model;

class CargosCriteriosAdicionaisModel extends Model{
    //Atributos
    protected $table = 'tb_cargos_criterios_adicionais';
    protected $primaryKey = 'pk_id_cargo_criterio';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'fk_id_cargo',
        'fk_id_criterio',
        'ds_pontuacao_minima',
        'ds_pontuacao_maxima',
        'ds_multiplicador',
        'ds_tipo_campo',
    ];
     protected $validationRules = [
        'fk_id_cargo'           => 'required',
        'fk_id_criterio'    => 'required',
        'ds_pontuacao_minima'   => 'required',
        'ds_pontuacao_maxima'   => 'required',
        'ds_tipo_campo'         => 'required',
    ];

    

    public function listarCriteriosDoCargo($idCargo){
        return $this->db->table('tb_cargos_criterios_adicionais')
            ->select([
                'tb_cargos_criterios_adicionais.pk_id_cargo_criterio',
                'tb_cargos_criterios_adicionais.fk_id_cargo',
                'tb_cargos_criterios_adicionais.fk_id_criterio',
                'tb_cargos_criterios_adicionais.ds_pontuacao_minima',
                'tb_cargos_criterios_adicionais.ds_pontuacao_maxima',
                'tb_cargos_criterios_adicionais.ds_tipo_campo',
                'tb_criterios_adicionais.ds_nome_criterio'
            ])
            ->join('tb_criterios_adicionais', 'tb_criterios_adicionais.pk_id_criterio = tb_cargos_criterios_adicionais.fk_id_criterio')
            ->where('tb_cargos_criterios_adicionais.fk_id_cargo', $idCargo)
            ->orderBy('tb_criterios_adicionais.ds_nome_criterio', 'ASC')
            ->get()
            ->getResult();
    }

}