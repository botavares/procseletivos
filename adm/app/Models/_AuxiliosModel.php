<?php
namespace App\Models;

use CodeIgniter\Model;

class AuxiliosModel extends Model{
    //Atributos
    protected $table = 'tb_auxilios';
    protected $primaryKey = 'pk_id_auxilio';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
       'ds_horas_diarias','ds_valor_bolsa','ds_valor_transporte'
    ];

    protected $validationRules = [
        'ds_horas_diarias' => 'required',
        'ds_valor_bolsa' => 'required',
        'ds_valor_transporte' => 'required'
    ];

    protected $returnType = 'object';

}