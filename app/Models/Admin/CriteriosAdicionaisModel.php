<?php
namespace App\Models\Admin;

use CodeIgniter\Model;

class CriteriosAdicionaisModel extends Model{
    //Atributos
    protected $table = 'tb_criterios_adicionais';
    protected $primaryKey = 'pk_id_criterio';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'fk_id_cargo',
        'ds_nome_criterio'
    ];

    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;

     public function listarCriteriosOrdenados(){
        return $this->orderBy('ds_nome_criterio', 'ASC')->findAll();
    }

    public function listarCriteriosPorCargo(int $idCargo){
        return $this->where('fk_id_cargo', $idCargo)
                    ->orWhere('fk_id_cargo IS NULL')
                    ->orderBy('ds_nome_criterio', 'ASC')
                    ->findAll();
    }
}