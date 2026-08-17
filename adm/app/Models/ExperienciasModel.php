<?php
namespace App\Models;

use CodeIgniter\Model;

class ExperienciasModel extends Model{
    protected $table = 'tb_experiencias';
    protected $primaryKey = 'pk_id_experiencia';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'ds_nome_experiencia',
        'ds_tipo_experiencia'
    ];

    protected $returnType = 'object';

    public function listarExperienciasOrdenadas(){
        return $this->orderBy('ds_nome_experiencia', 'ASC')->findAll();
    }
}
