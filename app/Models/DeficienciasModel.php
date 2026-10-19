<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class DeficienciasModel extends Model
{
    protected $table = 'tb_deficiencias';
    protected $primaryKey = 'pk_id_deficiencia';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'ds_nome_pne',
    ];

    protected $validationRules = [
        'ds_nome_pne' => 'required|string',
    ];
}
