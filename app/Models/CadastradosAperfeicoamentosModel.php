<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class CadastradosAperfeicoamentosModel extends Model
{
    protected $table = 'tb_cadastrados_aperfeicoamentos';
    protected $primaryKey = 'pk_id_aperfeicoamento';
    protected $useAutoIncrement = false;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'fk_id_cadastrado',
        'fk_id_edital',
        'fk_id_cargo',
        'fk_id_curso',
        'ds_quantidade',
        'ds_multiplicador',
    ];

    protected $validationRules = [
        'fk_id_cadastrado' => 'required|integer',
        'fk_id_edital' => 'required|integer',
        'fk_id_cargo' => 'required|integer',
        'fk_id_curso' => 'required|integer',
        'ds_quantidade' => 'permit_empty|string',
        'ds_multiplicador' => 'permit_empty|string',
    ];
}
