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
}
