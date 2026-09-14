<?php
namespace App\Models;

use CodeIgniter\Model;

class CadastradosExperienciasModel extends Model{
    //Atributos
    protected $table = 'tb_cadastrados_experiencias';
    protected $primaryKey = 'pk_id_experiencias';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'fk_id_cadastrado',
        'fk_id_edital',
        'fk_id_cargo',
        'fk_id_experiencia',
        'ds_quantidade',
        'ds_multiplicador',
    ];

    protected $returnType = 'object';

}