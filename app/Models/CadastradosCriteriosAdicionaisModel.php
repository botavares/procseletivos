<?php
namespace App\Models;

use CodeIgniter\Model;

class CadastradosCriteriosAdicionaisModel extends Model{
    //Atributos
    protected $table = 'tb_cadastrados_criterios';
    protected $primaryKey = 'pk_id_criterios';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'fk_id_cadastrado',
        'fk_id_edital',
        'fk_id_cargo',
        'fk_id_criterio',
        'ds_status',
        'ds_quantidade',
        'ds_multiplicador',
    ];

    protected $returnType = 'object';

}