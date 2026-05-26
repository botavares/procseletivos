<?php
namespace App\Models;

use CodeIgniter\Model;

class CargosModel extends Model{
    //Atributos
    protected $table = 'tb_cargos';
    protected $primaryKey = 'pk_id_cargo';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
       'fk_id_secretaria','ds_nome_cargo','ds_carga_horaria','ds_nivel'
    ];

    protected $validationRules = [
        'ds_nome_cargo' => 'required',
        'fk_id_secretaria' => 'required'
        
    ];

    protected $returnType = 'object';

    public function getCargos(){
        return $this->findAll();
    }
    public function getCargo($id){
        return $this->where('pk_id_cargo', $id)->first();
    }


    public function getCargosByEdital($idEdital){
        $builder = $this->db->table('tb_editais_cargos');
        $builder->select('tb_cargos.*');
        $builder->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_editais_cargos.fk_id_cargo');
        $builder->where('tb_editais_cargos.fk_id_edital', $idEdital);
        $query = $builder->get();
        return $query->getResult();
    }

    public function CargosPorEdital($cargo, $edital){
        $builder = $this->db->table('tb_editais_cargos');
        $builder->select('tb_cargos.*');
        $builder->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_editais_cargos.fk_id_cargo');
        $builder->where('tb_editais_cargos.fk_id_edital', $edital);
        $builder->where('tb_editais_cargos.fk_id_cargo', $cargo);
        $query = $builder->get();
        return $query->getRow();
    }

}