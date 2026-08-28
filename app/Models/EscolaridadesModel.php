<?php
namespace App\Models;

use CodeIgniter\Model;

class EscolaridadesModel extends Model{
    //Atributos
    protected $table = 'tb_escolaridades';
    protected $primaryKey = 'pk_id_escolaridade';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'ds_nome_escolaridade',
        'fk_id_nivel',
    ];

    protected $returnType = 'object';

    /*
    * Retorna os requisitos de escolaridades vinculados a um edital específico.
    */
    public function listarRequisitosEscolaridades($cargo = null){
        return $this->db->table('tb_cargos_escolaridades')
            ->select([
                'tb_cargos_escolaridades.fk_id_escolaridade',
                'tb_cargos_escolaridades.fk_id_cargo',
                'tb_escolaridades.ds_nome_escolaridade',
                
                'tb_cargos_escolaridades.ds_pontuacao_minima',
                'tb_cargos_escolaridades.ds_pontuacao_maxima',
                
                'tb_cargos_escolaridades.ds_tipo_campo'
            ])
            ->join('tb_escolaridades', 'tb_cargos_escolaridades.fk_id_escolaridade = tb_escolaridades.pk_id_escolaridade')
            ->join('tb_cargos', 'tb_cargos_escolaridades.fk_id_cargo = tb_cargos.pk_id_cargo')
            ->where('tb_cargos.pk_id_cargo', $cargo)
            
            ->orderBy('tb_escolaridades.fk_id_nivel', 'asc')
            ->orderBy('tb_escolaridades.pk_id_escolaridade', 'desc')
            ->get()
            ->getResult();
    }

}