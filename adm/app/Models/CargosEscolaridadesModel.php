<?php
namespace App\Models;

use CodeIgniter\Model;

class CargosEscolaridadesModel extends Model{
    //Atributos
    protected $table = 'tb_cargos_escolaridades';
    protected $primaryKey = 'pk_id_cargos_escolaridade';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'fk_id_cargo',
        'fk_id_escolaridade',
        'ds_pontuacao_minima',
        'ds_pontuacao_maxima',
        'ds_multiplicador',
        'ds_tipo_campo',
    ];
     protected $validationRules = [
        'fk_id_cargo'           => 'required',
        'fk_id_escolaridade'    => 'required',
        'ds_pontuacao_minima'   => 'required',
        'ds_pontuacao_maxima'   => 'required',
        'ds_tipo_campo'         => 'required',
    ];

    public function listarEscolaridadesDoCargo($idCargo){
        return $this->db->table('tb_cargos_escolaridades')
            ->select([
                'tb_cargos_escolaridades.pk_id_cargos_escolaridade',
                'tb_cargos_escolaridades.fk_id_cargo',
                'tb_cargos_escolaridades.fk_id_escolaridade',
                'tb_cargos_escolaridades.ds_pontuacao_minima',
                'tb_cargos_escolaridades.ds_pontuacao_maxima',
                'tb_cargos_escolaridades.ds_tipo_campo',
                'tb_escolaridades.ds_nome_escolaridade'
            ])
            ->join('tb_escolaridades', 'tb_escolaridades.pk_id_escolaridade = tb_cargos_escolaridades.fk_id_escolaridade')
            ->where('tb_cargos_escolaridades.fk_id_cargo', $idCargo)
            ->orderBy('tb_escolaridades.ds_nome_escolaridade', 'ASC')
            ->get()
            ->getResult();
    }

}