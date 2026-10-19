<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ArquivoModel extends Model
{
    protected $table = 'tb_arquivos_manifestos';
    protected $primaryKey = 'pk_id_postado';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'ds_protocolo',
        'ds_nome_arquivo',
    ];

    protected $validationRules = [
        'ds_protocolo' => 'required|string',
        'ds_nome_arquivo' => 'required|string',
    ];
}
