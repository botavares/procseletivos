<?php
namespace App\Models;

use CodeIgniter\Model;

class CriteriosAdicionaisModel extends Model{
    //Atributos
    protected $table = 'tb_criterios_adicionais';
    protected $primaryKey = 'pk_id_criterio';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'ds_nome_criterio'
    ];

    protected $returnType = 'object';

     public function listarCriteriosOrdenados(){
        return $this->orderBy('ds_nome_criterio', 'ASC')->findAll();
    }
}