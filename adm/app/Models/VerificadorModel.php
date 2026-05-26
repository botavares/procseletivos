<?php
namespace App\Models;

use CodeIgniter\Model;

class VerificadorModel extends Model{
    //Atributos
    protected $table = 'tb_verificador_contratos';
    protected $primaryKey = 'pk_id_contrato';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
      'pk_id_contrato', 
      'ds_verificador',
       
    ];

    protected $validationRules = [
      'ds_verificador'=> 'required',
        
    ];

    protected $returnType = 'object';

}