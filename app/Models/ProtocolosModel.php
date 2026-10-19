<?php
declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ProtocolosModel extends Model
{
    protected $table = 'tb_cadastrados_protocolo';
    protected $primaryKey = 'pk_id_processo';
    protected $useAutoIncrement = true;
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $returnType = 'object';
    protected $allowedFields = [
        'fk_id_cadastrado','fk_id_secretaria','fk_id_edital','fk_id_cargo','ds_cpf_cadastrado','ds_protocolo','ds_data_protocolo','ds_hora_protocolo','ds_ip_protocolo'
    ];

    protected $validationRules = [
        'fk_id_cadastrado'  => 'required|integer',
        'fk_id_secretaria'  => 'permit_empty|integer',
        'fk_id_edital'      => 'required|integer',
        'fk_id_cargo'       => 'required|integer',
        'ds_cpf_cadastrado' => 'required',
        'ds_protocolo'      => 'required',
        'ds_data_protocolo' => 'required',
        'ds_hora_protocolo' => 'required',
    ];
}
