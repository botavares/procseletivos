<?php
declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class VerificadorModel extends Model
{
    protected $table = 'tb_verificador_contratos';
    protected $primaryKey = 'pk_id_contrato';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['ds_verificador'];
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $validationRules = [];
    protected $returnType = 'object';
}
