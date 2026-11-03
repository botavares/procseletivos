<?php
namespace App\Models\Admin;

use CodeIgniter\Model;

class ExperienciasModel extends Model{
    protected $table = 'tb_experiencias';
    protected $primaryKey = 'pk_id_experiencia';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'fk_id_cargo',
        'ds_nome_experiencia',
        'ds_tipo_experiencia'
    ];

    protected $returnType = 'object';

    public function listarExperienciasOrdenadas(){
        return $this->orderBy('ds_nome_experiencia', 'ASC')->findAll();
    }

    public function listarExperienciasPorCargo(int $idCargo){
        return $this->where('fk_id_cargo', $idCargo)
                    ->orWhere('fk_id_cargo IS NULL')
                    ->orderBy('ds_nome_experiencia', 'ASC')
                    ->findAll();
    }
}
