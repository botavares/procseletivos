<?php
namespace App\Models;

use CodeIgniter\Model;

namespace App\Models;

use CodeIgniter\Model;

class VerificadorModel extends Model{
    protected $table = 'tb_verificador_contratos';
    protected $primaryKey = 'pk_id_contrato';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['pk_id_contrato', 'ds_verificador'];

      protected $returnType = 'object';

      
}