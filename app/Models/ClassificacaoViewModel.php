<?php
declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ClassificacaoViewModel extends Model
{
    protected $table = 'vw_classificacao_candidatos';
    protected $primaryKey = 'fk_id_candidato';
    protected $useAutoIncrement = false;
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [];
    protected $validationRules = [];
    protected $returnType = 'object';
}
