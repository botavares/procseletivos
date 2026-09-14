<?php
namespace App\Models;

use CodeIgniter\Model;

class CadastradosEscolaridadesModel extends Model{
    //Atributos
    protected $table = 'tb_cadastrados_escolaridades';
    protected $primaryKey = 'pk_id_escolaridade';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'fk_id_cadastrado',
        'fk_id_edital',
        'fk_id_cargo',
        'fk_id_escolaridade',
        'ds_quantidade',
        'ds_multiplicador',
    ];

    protected $returnType = 'object';

}