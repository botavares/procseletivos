<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class BairrosModel extends Model
{
    protected $table = 'tb_bairros';
    protected $primaryKey = 'pk_id_bairro';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'ds_nome_bairro',
        'fk_id_cidade',
        'fk_id_regiao',
        'ds_anoensino',
    ];

    protected $validationRules = [
        'ds_nome_bairro' => 'required|string',
        'fk_id_cidade' => 'required|integer',
        'fk_id_regiao' => 'required|integer',
        'ds_anoensino' => 'required|integer',
    ];
}
