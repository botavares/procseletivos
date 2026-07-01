<?php

namespace App\Models;

use CodeIgniter\Model;

class SecretariasModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tb_secretarias';
    protected $primaryKey       = 'pk_id_secretaria';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ds_nome_secretaria',
        'ds_nome_secretario',
        'ds_responsavel_processo',
        'ds_sigla_secretaria',
        'ds_email_secretaria',
        'ds_telefone_secretaria',
        
        
     ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'ds_deletado';

    // Validation
    protected $validationRules      = [
        'ds_nome_secretaria' => 'required',
        'ds_sigla_secretaria' => 'required',
        'ds_email_secretaria' => 'required',
        'ds_nome_secretario' => 'required',
        'ds_responsavel_processo' => 'required'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
