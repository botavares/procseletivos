<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class CriteriosAdicionaisModel extends Model
{
    protected $table = 'tb_criterios_adicionais';
    protected $primaryKey = 'pk_id_criterio';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'fk_id_cargo',
        'ds_nome_criterio',
    ];

    protected $validationRules = [
        'fk_id_cargo' => 'permit_empty|integer',
        'ds_nome_criterio' => 'required|string',
    ];

    public function listarCriteriosPorCargo(int $idCargo)
    {
        return $this->where('fk_id_cargo', $idCargo)
                    ->orWhere('fk_id_cargo IS NULL')
                    ->orderBy('ds_nome_criterio', 'ASC')
                    ->findAll();
    }

    /**
     * Retorna os critérios adicionais vinculados a um edital específico.
     */
    public function listarRequisitosCriteriosAdicionais($cargo = null)
    {
        return $this->db->table('tb_cargos_criterios_adicionais')
            ->select([
                'tb_cargos.pk_id_cargo',
                'tb_criterios_adicionais.ds_nome_criterio',
                'tb_cargos_criterios_adicionais.fk_id_cargo',
                'tb_cargos_criterios_adicionais.fk_id_criterio',
                'tb_cargos_criterios_adicionais.ds_pontuacao_minima',
                'tb_cargos_criterios_adicionais.ds_pontuacao_maxima',
                'tb_cargos_criterios_adicionais.ds_tipo_campo',
            ])
            ->join(
                'tb_criterios_adicionais',
                'tb_cargos_criterios_adicionais.fk_id_criterio = tb_criterios_adicionais.pk_id_criterio'
            )
            ->join(
                'tb_cargos',
                'tb_cargos_criterios_adicionais.fk_id_cargo = tb_cargos.pk_id_cargo'
            )
            ->where('tb_cargos.pk_id_cargo', $cargo)
            ->get()
            ->getResult();
    }
}
