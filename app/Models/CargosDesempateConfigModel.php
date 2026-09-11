<?php

namespace App\Models;

use CodeIgniter\Model;

class CargosDesempateConfigModel extends Model
{
    protected $table = 'tb_cargos_desempates_config';
    protected $primaryKey = 'pk_id_desempate';
    protected $allowedFields = [
        'fk_id_cargo',
        'ds_ordem',
        'ds_tipo_criterio',
        'fk_id_referencia',
        'ds_direcao',
        'ds_descricao',
        'ds_parametro_extra',
    ];
    protected $returnType = 'object';

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
