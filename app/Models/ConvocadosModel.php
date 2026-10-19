<?php
declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ConvocadosModel extends Model
{
    protected $table = 'tb_dados_convocacao';
    protected $primaryKey = 'pk_id_convocacao';
    protected $useAutoIncrement = true;
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $returnType = 'object';
    protected $allowedFields = [
        'fk_id_candidato','fk_id_edital','fk_id_curso','ds_comparecimento','ds_mensagem','ds_data','ds_hora','ds_interesse','ds_status'
    ];

    protected $validationRules = [
        'fk_id_candidato'   => 'required|integer',
        'fk_id_edital'      => 'required|integer',
        'fk_id_curso'       => 'required|integer',
        'ds_comparecimento' => 'required',
        'ds_status'         => 'required',
        'ds_data'           => 'required',
        'ds_hora'           => 'required',
    ];
}
