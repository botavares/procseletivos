<?php
namespace App\Models;

use CodeIgniter\Model;

class CadastradosAperfeicoamentosModel extends Model{
    //Atributos
    protected $table = 'tb_cadastrados_aperfeicoamentos';
    protected $primaryKey = 'pk_id_aperfeicoamento';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'fk_id_cadastrado',
        'fk_id_edital', 
        'fk_id_cargo', 
        'fk_id_curso',
        'ds_quantidade',
        'ds_multiplicador',
    ];

    protected $returnType = 'object';

}