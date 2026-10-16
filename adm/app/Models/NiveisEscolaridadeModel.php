<?php

namespace App\Models;

use CodeIgniter\Model;

class NiveisEscolaridadeModel extends Model
{
    protected $table = 'tb_niveis_escolaridade';
    protected $primaryKey = 'pk_id_niveis_escolaridade';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'ds_nome'
    ];

    protected $returnType = 'object';
}
