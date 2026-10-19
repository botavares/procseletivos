<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class PerguntasFrequentesModel extends Model
{
    protected $table = 'tb_perguntas_frequentes';
    protected $primaryKey = 'pk_id_pergunta';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'ds_pergunta',
        'ds_resposta',
    ];

    protected $validationRules = [
        'ds_pergunta' => 'required|string',
        'ds_resposta' => 'required|string',
    ];
}
