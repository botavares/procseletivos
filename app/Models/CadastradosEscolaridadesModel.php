<?php
namespace App\Models;

use CodeIgniter\Model;

class CadastradosEscolaridadesModel extends Model{
    //Atributos
    protected $table = 'tb_cadastrados_escolaridades';
    protected $primaryKey = 'fk_id_cadastrado';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'fk_id_edital',
        'fk_id_cargo',
        'fk_id_escolaridade',
        'ds_status',
        'ds_quantidade',
        'ds_multiplicador',
    ];

    protected $returnType = 'object';

}