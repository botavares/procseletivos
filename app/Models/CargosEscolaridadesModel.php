<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class CargosEscolaridadesModel extends Model
{
    protected $table = 'tb_cargos_escolaridades';
    protected $primaryKey = 'pk_id_cargo_escolaridade';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'fk_id_cargo',
        'fk_id_escolaridade',
        'ds_pontuacao_minima',
        'ds_pontuacao_maxima',
        'ds_tipo_campo',
    ];

    protected $validationRules = [
        'fk_id_cargo' => 'required|integer',
        'fk_id_escolaridade' => 'required|integer',
        'ds_pontuacao_minima' => 'permit_empty|string',
        'ds_pontuacao_maxima' => 'permit_empty|string',
        'ds_tipo_campo' => 'permit_empty|string',
    ];
}
