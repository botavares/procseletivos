<?php
namespace App\Models;

use CodeIgniter\Model;

class AperfeicoamentosModel extends Model{
    //Atributos
    protected $table = 'tb_cursos_aperfeicoamentos';
    protected $primaryKey = 'pk_id_curso';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'ds_nome_curso',
    ];

    protected $returnType = 'object';

    /**
     * Retorna os cursos de aperfeiçoamentos vinculados a um edital específico.
     */
    public function listarRequisitosAperfeicoamentos($cargo = null){
        return $this->db->table('tb_cargos_aperfeicoamentos')
            ->select([
                'tb_cargos.pk_id_cargo',
                'tb_cargos_aperfeicoamentos.fk_id_curso',
                'tb_cursos_aperfeicoamentos.ds_nome_curso',
                'tb_cargos_aperfeicoamentos.ds_tipo_campo',
                'tb_cargos_aperfeicoamentos.ds_pontuacao_minima',
                'tb_cargos_aperfeicoamentos.ds_pontuacao_maxima'
            ])
            ->join(
                'tb_cursos_aperfeicoamentos',
                'tb_cargos_aperfeicoamentos.fk_id_curso = tb_cursos_aperfeicoamentos.pk_id_curso'
            )
            ->join(
                'tb_cargos',
                'tb_cargos_aperfeicoamentos.fk_id_cargo = tb_cargos.pk_id_cargo'
            )
            ->where('tb_cargos.pk_id_cargo', $cargo)
            ->get()
            ->getResultArray();
    }
}