<?php
namespace App\Models;

use CodeIgniter\Model;

class CargosEscolaridadesEditaisModel extends Model{
    //Atributos
    protected $table = 'tb_cargos_escolaridades_editais';
    protected $primaryKey = ['fk_id_cargo', 'fk_id_escolaridade'];
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'ds_obrigatorio',
        'ds_quantidade_minima',
        'ds_quantidade_maxima',
        'ds_multiplicador',
        'ds_tipo_campo',
        'ds_desempate'
    ];

    protected $returnType = 'object';

}