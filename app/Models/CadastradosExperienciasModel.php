<?php
namespace App\Models;

use CodeIgniter\Model;

class CadastradosExperienciasModel extends Model{
    //Atributos
    protected $table = 'tb_cadastrados_experiencias';
    protected $primaryKey = 'fk_id_cadastrado';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'fk_id_edital',
        'fk_id_cargo',
        'fk_id_experiencia',
        'ds_status',
        'ds_quantidade',
        'ds_multiplicador',
    ];

    protected $returnType = 'object';

}