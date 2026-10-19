<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class AcademicosModel extends Model
{
    protected $table = 'tb_dados_academicos';
    protected $primaryKey = 'pk_id_candidato';
    protected $useAutoIncrement = false;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'pk_id_candidato',
        'fk_id_curso',
        'ds_ensino_medio',
        'ds_categoria',
        'ds_periodo',
        'ds_faculdade',
        'ds_manha',
        'ds_tarde',
        'ds_noite',
        'ds_registro_academico',
        'ds_data_cadastro',
        'ds_hora_cadastro',
        'ds_data_alteracao',
        'ds_hora_alteracao',
        'ds_ip_cadastro',
    ];

    protected $validationRules = [
        'fk_id_curso' => 'required|integer',
        'ds_ensino_medio' => 'required|string',
        'ds_categoria' => 'required|string',
        'ds_periodo' => 'required|string',
        'ds_faculdade' => 'required|string',
    ];
}
