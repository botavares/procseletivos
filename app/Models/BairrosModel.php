<?php
namespace App\Models;

use CodeIgniter\Model;

class BairrosModel extends Model{
    //Atributos
    protected $table = 'tb_bairros';
    protected $primaryKey = 'pk_id_bairro';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'ds_nome_bairro',
        'fk_id_cidade',
        'fk_id_regiao',
        'ds_anoensino',
    ];

    protected $returnType = 'object';

}