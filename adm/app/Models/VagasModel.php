<?php
namespace App\Models;

use CodeIgniter\Model;

class VagasModel extends Model{
    //Atributos
    protected $table = 'tb_vagas_setores_cursos';
    protected $primaryKey = 'pk_id_vaga';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
       'fk_id_setor','fk_id_curso','ds_vagas','ds_responsavel','ds_telefone','ds_observacao'
    ];

    protected $validationRules = [
        'fk_id_setor'   => 'required',
        'fk_id_curso'   => 'required',
        'ds_vagas'      => 'required',
        'ds_responsavel'=> 'required',
        'ds_telefone'   => 'required',
    ];

    protected $returnType = 'object';

    public function setoresComVagas($idCurso){
        $this-> select('tb_vagas_setores_cursos.*, tb_setores.pk_id_setor,tb_setores.ds_nome_setor');
        $this-> join('tb_setores', 'tb_setores.pk_id_setor = tb_vagas_setores_cursos.fk_id_setor');
        $this-> where('tb_vagas_setores_cursos.fk_id_curso', $idCurso);
        $this-> where('tb_vagas_setores_cursos.ds_vagas >', 0);
        $this-> orderBy('tb_setores.ds_nome_setor', 'asc');
        return $this-> findAll();
    }

    public function vagasSetorCurso($idVaga){
        $this-> select('tb_vagas_setores_cursos.*, tb_setores.ds_nome_setor,tb_secretarias.ds_nome_secretaria,
        tb_cursos.ds_nome_curso');
        $this-> join('tb_setores', 'tb_setores.pk_id_setor = tb_vagas_setores_cursos.fk_id_setor');
        $this-> join('tb_secretarias', 'tb_secretarias.pk_id_secretaria = tb_setores.fk_id_secretaria');
        $this-> join('tb_cursos', 'tb_cursos.pk_id_curso = tb_vagas_setores_cursos.fk_id_curso');
        $this-> where('tb_vagas_setores_cursos.pk_id_vaga', $idVaga);
        $this-> orderBy('tb_setores.ds_nome_setor', 'asc');
        return $this-> first();
    }

    public function vagas(){
        $this-> select('tb_vagas_setores_cursos.*, tb_setores.ds_nome_setor,tb_secretarias.ds_nome_secretaria,
        tb_cursos.ds_nome_curso');
        $this-> join('tb_setores', 'tb_setores.pk_id_setor = tb_vagas_setores_cursos.fk_id_setor');
        $this-> join('tb_secretarias', 'tb_secretarias.pk_id_secretaria = tb_setores.fk_id_secretaria');
        $this-> join('tb_cursos', 'tb_cursos.pk_id_curso = tb_vagas_setores_cursos.fk_id_curso');
        $this-> orderBy('tb_setores.ds_nome_setor', 'asc');
        return $this-> findAll();
    }
    public function debitarVaga(int $idSetor, int $idCurso): void{
        $builder = $this->builder();

        $builder
            ->where('fk_id_setor', $idSetor)
            ->where('fk_id_curso', $idCurso)
            ->where('ds_vagas >', 0)
            ->set('ds_vagas', 'ds_vagas - 1', false)
            ->update();

        if ($this->db->affectedRows() === 0) {
            throw new \RuntimeException('Não há vagas disponíveis para este setor e curso');
        }
    }

    public function creditarVaga(int $idSetor, int $idCurso): void{
        $builder = $this->builder();

        $builder
            ->where('fk_id_setor', $idSetor)
            ->where('fk_id_curso', $idCurso)
            ->set('ds_vagas', 'ds_vagas + 1', false)
            ->update();
        
    }

}