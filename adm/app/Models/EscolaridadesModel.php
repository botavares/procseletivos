<?php
namespace App\Models;

use CodeIgniter\Model;

class EscolaridadesModel extends Model{
    //Atributos
    protected $table = 'tb_escolaridades';
    protected $primaryKey = 'pk_id_escolaridade';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'ds_nome_escolaridade','fk_id_nivel'
    ];

    protected $returnType = 'object';
}