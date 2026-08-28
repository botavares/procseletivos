<?php
namespace App\Models;

use CodeIgniter\Model;

class CargosAperfeicoamentosModel extends Model{
    //Atributos
    protected $table = 'tb_cargos_aperfeicoamentos';
    protected $primaryKey = ['pk_id_cargo_aperfeicoamento'];
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'fk_id_cargo',
        'fk_id_curso',
        'ds_pontuacao_maxima',
        'ds_quantidade_minima',
        'ds_tipo_campo',
    ];

    protected $returnType = 'object';

}