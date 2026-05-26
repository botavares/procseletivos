<?php
namespace App\Models;

use CodeIgniter\Model;

class MotivosRescisaoModel extends Model{
    //Atributos
    protected $table = 'tb_rescisao_motivos';
    protected $primaryKey = 'pk_id_motivo';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'ds_descricao_motivo',
        'ds_status'
    ];

    protected $returnType = 'object';

}