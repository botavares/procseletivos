<?php
namespace App\Models;

use CodeIgniter\Model;

class CargosExperienciasModel extends Model{
    //Atributos
    protected $table = 'tb_cargos_experiencias';
    protected $primaryKey = 'pk_id_cargo_experiencia';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'fk_id_cargo',
        'fk_id_experiencia',
        'ds_pontuacao_minima',
        'ds_pontuacao_maxima',
        'ds_tipo_campo',
    ];

    protected $returnType = 'object';

}