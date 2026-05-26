<?php
namespace App\Models;

use CodeIgniter\Model;

class DeficienciasModel extends Model{
    //Atributos
    protected $table = 'tb_deficiencias';
    protected $primaryKey = 'pk_id_deficiencia';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $allowedFields = [
        'ds_nome_pne',
        
    ];

    

}