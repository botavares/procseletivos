<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class CargosDesempateConfigModel extends Model
{
    protected $table = 'tb_cargos_desempates_config';
    protected $primaryKey = 'pk_id_desempate';
    protected $allowedFields = [
        'fk_id_cargo',
        'fk_id_processo_seletivo',
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
     * @param int|null $processo
     * @return array
     */
    public function listarPorCargo(int $cargo, ?int $processo = null): array
    {
        $builder = $this->where('fk_id_cargo', $cargo);

        if ($processo !== null) {
            $builder->where('fk_id_processo_seletivo', $processo);
        } else {
            $builder->where('fk_id_processo_seletivo IS NULL');
        }

        return $builder->orderBy('ds_ordem', 'ASC')
                       ->findAll();
    }

    /**
     * Remove todos os criterios de desempate de um cargo.
     *
     * @param int $cargo
     * @param int|null $processo
     * @return bool
     */
    public function limparPorCargo(int $cargo, ?int $processo = null): bool
    {
        $builder = $this->where('fk_id_cargo', $cargo);

        if ($processo !== null) {
            $builder->where('fk_id_processo_seletivo', $processo);
        } else {
            $builder->where('fk_id_processo_seletivo IS NULL');
        }

        return $builder->delete();
    }
}
