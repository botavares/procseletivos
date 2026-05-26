<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassificacaoViewModel extends Model
{
    protected $table = 'vw_classificacao_candidatos';
    protected $primaryKey = 'fk_id_candidato';
    protected $returnType = 'object';
    protected $allowedFields = [];
    protected $useAutoIncrement = false;
}