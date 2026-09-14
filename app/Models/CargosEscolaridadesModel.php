<?php
namespace App\Models;

use CodeIgniter\Model;

class CargosEscolaridadesModel extends Model{
    //Atributos
    protected $table = 'tb_cargos_escolaridades';
    protected $primaryKey = 'pk_id_cargo_escolaridade';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'fk_id_cargo',
        'fk_id_escolaridade',
        'ds_pontuacao_minima',
        'ds_pontuacao_maxima',
        'ds_tipo_campo',
    ];

    protected $returnType = 'object';

}