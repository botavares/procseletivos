<?php
namespace App\Models;

use CodeIgniter\Model;

class CargosAperfeicoamentosModel extends Model{
    //Atributos
    protected $table = 'tb_cargos_aperfeicoamentos';
    protected $primaryKey = 'pk_id_cargo_aperfeicoamento';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'fk_id_cargo',
        'fk_id_curso',
        'ds_pontuacao_minima',
        'ds_pontuacao_maxima',
        'ds_tipo_campo',
    ];

    protected $returnType = 'object';

    public function listarAperfeicoamentosDoCargo($idCargo){
        return $this->db->table('tb_cargos_aperfeicoamentos')
            ->select([
                'tb_cargos_aperfeicoamentos.pk_id_cargo_aperfeicoamento',
                'tb_cargos_aperfeicoamentos.fk_id_cargo',
                'tb_cargos_aperfeicoamentos.fk_id_curso',
                'tb_cargos_aperfeicoamentos.ds_pontuacao_minima',
                'tb_cargos_aperfeicoamentos.ds_pontuacao_maxima',
                'tb_cargos_aperfeicoamentos.ds_tipo_campo',
                'tb_cursos_aperfeicoamentos.ds_nome_curso'
            ])
            ->join('tb_cursos_aperfeicoamentos', 'tb_cursos_aperfeicoamentos.pk_id_curso = tb_cargos_aperfeicoamentos.fk_id_curso')
            ->where('tb_cargos_aperfeicoamentos.fk_id_cargo', $idCargo)
            ->orderBy('tb_cursos_aperfeicoamentos.ds_nome_curso', 'ASC')
            ->get()
            ->getResult();
    }
}
