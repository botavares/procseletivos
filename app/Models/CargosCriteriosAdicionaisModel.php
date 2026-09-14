<?php
namespace App\Models;

use CodeIgniter\Model;

class CargosCriteriosAdicionaisModel extends Model{
    //Atributos
    protected $table = 'tb_cargos_criterios_adicionais';
    protected $primaryKey = 'pk_id_cargo_criterio';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'fk_id_cargo',
        'fk_id_criterio',
        'ds_pontuacao_minima',
        'ds_pontuacao_maxima',
        'ds_tipo_campo',
    ];

    protected $returnType = 'object';

}