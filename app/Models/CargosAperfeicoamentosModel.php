<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class CargosAperfeicoamentosModel extends Model
{
    protected $table = 'tb_cargos_aperfeicoamentos';
    protected $primaryKey = 'pk_id_cargo_aperfeicoamento';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'fk_id_secretaria',
        'fk_id_processo_seletivo',
        'fk_id_cargo',
        'fk_id_curso',
        'ds_pontuacao_maxima',
        'ds_quantidade_minima',
        'ds_tipo_campo',
    ];

    protected $validationRules = [
        'fk_id_cargo' => 'required|integer',
        'fk_id_curso' => 'required|integer',
        'ds_pontuacao_maxima' => 'permit_empty|string',
        'ds_quantidade_minima' => 'permit_empty|string',
        'ds_tipo_campo' => 'permit_empty|string',
    ];

    /**
     * Verifica se existe registro vinculado ao processo/cargo/secretaria.
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
