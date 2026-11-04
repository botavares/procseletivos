<?php
namespace App\Models\Admin;

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
        'ds_nome_cargo'    => 'required',
        'fk_id_secretaria' => 'required|integer',
    ];

    protected $validationMessages = [
        'ds_nome_cargo' => [
            'required' => 'O nome do cargo é obrigatório.',
        ],
        'fk_id_secretaria' => [
            'required' => 'A secretaria do usuário não está definida na sessão. Faça login novamente.',
            'integer'  => 'O ID da secretaria deve ser um número inteiro.',
        ],
    ];

    protected $returnType = 'object';

    public function getCargos(){
        return $this->findAll();
    }
    public function getCargo($id){
        return $this->where('pk_id_cargo', $id)->first();
    }


    public function getCargosByProcesso($idProcesso){
        $builder = $this->db->table('tb_processos_cargos');
        $builder->select('tb_cargos.*');
        $builder->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_processos_cargos.fk_id_cargo');
        $builder->where('tb_processos_cargos.fk_id_processo', $idProcesso);
        $query = $builder->get();
        return $query->getResult();
    }

    public function CargosPorProcesso($cargo, $processo){
        $builder = $this->db->table('tb_processos_cargos');
        $builder->select('tb_cargos.*');
        $builder->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_processos_cargos.fk_id_cargo');
        $builder->where('tb_processos_cargos.fk_id_processo', $processo);
        $builder->where('tb_processos_cargos.fk_id_cargo', $cargo);
        $query = $builder->get();
        return $query->getRow();
    }

}