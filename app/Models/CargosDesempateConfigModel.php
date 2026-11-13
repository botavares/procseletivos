<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class CargosDesempateConfigModel extends Model
{
    protected $table = 'tb_classificacao_desempates_config';
    protected $primaryKey = 'pk_id_desempate';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'fk_id_secretaria',
        'fk_id_processo_seletivo',
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
     * @param int|null $processo
     * @param int|null $secretaria
     * @return array
     */
    public function listarPorCargo(int $cargo, ?int $processo = null, ?int $secretaria = null): array
    {
        $builder = $this->where('fk_id_cargo', $cargo);

        if ($secretaria !== null) {
            $builder->where('fk_id_secretaria', $secretaria);
        }

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
     * @param int|null $secretaria
     * @return bool
     */
    public function limparPorCargo(int $cargo, ?int $processo = null, ?int $secretaria = null): bool
    {
        $builder = $this->where('fk_id_cargo', $cargo);

        if ($secretaria !== null) {
            $builder->where('fk_id_secretaria', $secretaria);
        }

        if ($processo !== null) {
            $builder->where('fk_id_processo_seletivo', $processo);
        } else {
            $builder->where('fk_id_processo_seletivo IS NULL');
        }

        return $builder->delete();
    }

    /**
     * Verifica se existe configuração de desempate vinculada ao processo/cargo/secretaria.
     */
    public function existePorProcessoCargo(int $idProcesso, int $idCargo, ?int $idSecretaria = null): bool
    {
        $builder = $this->where('fk_id_processo_seletivo', $idProcesso)
                        ->where('fk_id_cargo', $idCargo);

        if ($idSecretaria !== null && $idSecretaria > 0) {
            $builder->where('fk_id_secretaria', $idSecretaria);
        }

        return $builder->first() !== null;
    }
}
