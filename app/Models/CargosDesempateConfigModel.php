<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class CargosDesempateConfigModel extends Model
{
    protected $table = 'tb_cargos_desempates_config';
    protected $primaryKey = 'pk_id_desempate';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'fk_id_cargo',
        'ds_ordem',
        'ds_tipo_criterio',
        'fk_id_referencia',
        'ds_direcao',
        'ds_descricao',
        'ds_parametro_extra',
    ];

    protected $validationRules = [
        'fk_id_cargo' => 'required|integer',
        'ds_ordem' => 'required|string',
        'ds_tipo_criterio' => 'required|string',
        'fk_id_referencia' => 'permit_empty|integer',
        'ds_direcao' => 'permit_empty|string',
        'ds_descricao' => 'permit_empty|string',
        'ds_parametro_extra' => 'permit_empty|string',
    ];

    /**
     * Lista a configuracao de desempate ordenada por ds_ordem.
     *
     * @param int $cargo
     * @return array
     */
    public function listarPorCargo(int $cargo): array
    {
        return $this->where('fk_id_cargo', $cargo)
                    ->orderBy('ds_ordem', 'ASC')
                    ->findAll();
    }

    /**
     * Remove todos os criterios de desempate de um cargo.
     *
     * @param int $cargo
     * @return bool
     */
    public function limparPorCargo(int $cargo): bool
    {
        return $this->where('fk_id_cargo', $cargo)->delete();
    }
}
