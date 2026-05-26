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

    public function listarStatusEscolaridades($cargo, $status){
        return $this->db->table('tb_cargos_escolaridades_editais')
            ->select([
                'tb_cargos_escolaridades_editais.fk_id_escolaridade',
                'tb_cargos_escolaridades_editais.fk_id_cargo',
                'tb_escolaridades.ds_nome_escolaridade',
                'tb_cargos_escolaridades_editais.ds_obrigatorio',
                'tb_cargos_escolaridades_editais.ds_pontuacao_minima',
                'tb_cargos_escolaridades_editais.ds_pontuacao_maxima',
                'tb_cargos_escolaridades_editais.ds_multiplicador',
                'tb_cargos_escolaridades_editais.ds_tipo_campo'
            ])
            ->join('tb_escolaridades', 'tb_cargos_escolaridades_editais.fk_id_escolaridade = tb_escolaridades.pk_id_escolaridade')
            ->join('tb_cargos', 'tb_cargos_escolaridades_editais.fk_id_cargo = tb_cargos.pk_id_cargo')
            ->where('tb_cargos.pk_id_cargo', $cargo)
            ->where('tb_cargos_escolaridades_editais.ds_obrigatorio', $status)
            ->orderBy('tb_escolaridades.fk_id_nivel', 'asc')
            ->get()
            ->getResult();
    }

}