<?php
namespace App\Models;

use CodeIgniter\Model;

class AbrangenciasModel extends Model{
    //Atributos
    protected $table = 'tb_abrangencias';
    protected $primaryKey = 'pk_id_abrangencia';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
       'ds_nome_abrangencia','ds_status'
    ];

    protected $validationRules = [
        'ds_nome_abrangencia' => 'required',
        'ds_status' => 'required',
    ];

    protected $returnType = 'object';

    public function getAbrangencias(){
        return $this->findAll();
    }
    public function getAbrangencia($id){
        return $this->where('pk_id_abrangencia', $id)->first();
    }
        public function getCursosAbrangencia($id){
        $this->select('pk_id_curso, ds_nome_curso')
            ->join('tb_cursos', 'tb_abrangencias.pk_id_abrangencia = tb_cursos.fk_id_abrangencia');
        return $this->findAll();
        
    }
}