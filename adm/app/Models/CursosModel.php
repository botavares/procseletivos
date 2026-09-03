<?php
namespace App\Models;

use CodeIgniter\Model;

class CursosModel extends Model{
    //Atributos
    protected $table = 'tb_cursos_aperfeicoamentos';
    protected $primaryKey = 'pk_id_curso';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
       'ds_nome_curso'
    ];

    protected $validationRules = [
        'ds_nome_curso' => 'required',
    ];

    protected $returnType = 'object';

    public function getCursos(){
        return $this->findAll();
    }
    public function getCurso($id){
        return $this->where('pk_id_curso', $id)->first();
    }


    public function getCursosByEdital($idEdital){
        $builder = $this->db->table('tb_editais_cargos');
        $builder->select('tb_cursos_aperfeicoamentos.*');
        $builder->join('tb_cursos_aperfeicoamentos', 'tb_cursos_aperfeicoamentos.pk_id_curso = tb_editais_cargos.fk_id_curso');
        $builder->where('tb_editais_cargos.fk_id_edital', $idEdital);
        $query = $builder->get();
        return $query->getResult();
    }

     public function listarCursosOrdenados(){
        return $this->orderBy('ds_nome_curso', 'ASC')->findAll();
    }

    public function CursosPorEdital($curso, $edital){
        $builder = $this->db->table('tb_editais_cargos');
        $builder->select('tb_cursos_aperfeicoamentos.*');
        $builder->join('tb_cursos_aperfeicoamentos', 'tb_cursos_aperfeicoamentos.pk_id_curso = tb_editais_cargos.fk_id_curso');
        $builder->where('tb_editais_cargos.fk_id_edital', $edital);
        $builder->where('tb_editais_cargos.fk_id_cargo', $cargo);
        $query = $builder->get();
        return $query->getRow();
    }

}