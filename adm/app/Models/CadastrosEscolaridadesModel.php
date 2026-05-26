<?php
namespace App\Models;

use CodeIgniter\Model;

class CadastrosEscolaridadesModel extends Model{
    //Atributos
    protected $table = 'tb_cadastrados_escolaridades';
    protected $primaryKey = 'pk_id_escolaridades';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'fk_id_edital',
        'fk_id_cargo',
        'fk_id_escolaridade',
        'ds_status',
        'ds_quantidade',
        'ds_multiplicador',
    ];

    protected $returnType = 'object';


    public function buscarEscolaridadesParaClassificacao($cadastrado, $edital, $cargo)
    {
        return $this->select([
        'tb_cadastrados_escolaridades.fk_id_cadastrado',
        'tb_cadastrados_escolaridades.fk_id_edital',
        'tb_cadastrados_escolaridades.fk_id_escolaridade',
        'tb_cadastrados_escolaridades.ds_status',
        'tb_cadastrados_escolaridades.ds_quantidade',
        'tb_cadastrados_escolaridades.ds_multiplicador',
        'tb_escolaridades.ds_nome_escolaridade',
        'tb_cargos_escolaridades_editais.ds_desempate',
        'tb_cargos_escolaridades_editais.ds_pontuacao_maxima'
    ])
    ->join(
        'tb_cargos_escolaridades_editais',
        'tb_cadastrados_escolaridades.fk_id_cargo = tb_cargos_escolaridades_editais.fk_id_cargo
         AND tb_cadastrados_escolaridades.fk_id_escolaridade = tb_cargos_escolaridades_editais.fk_id_escolaridade
         '
    )
    ->join(
        'tb_escolaridades',
        'tb_cargos_escolaridades_editais.fk_id_escolaridade = tb_escolaridades.pk_id_escolaridade'
    )
    ->where([
        'tb_cadastrados_escolaridades.fk_id_edital'     => $edital,
        'tb_cadastrados_escolaridades.fk_id_cargo'      => $cargo,
        'tb_cadastrados_escolaridades.fk_id_cadastrado' => $cadastrado,
    ])
    ->findAll();
    }
    public function listarEscolaridades($idCargo){
        return $this->db->table('tb_cargos_escolaridades_editais esc')
        ->select("
            esc.fk_id_escolaridade,
            e.ds_nome_escolaridade,
            esc.ds_pontuacao_minima,
            esc.ds_pontuacao_maxima,
            esc.ds_multiplicador,
            esc.ds_tipo_campo,
            
        ")
        ->join(
            'tb_escolaridades e',
            'e.pk_id_escolaridade = esc.fk_id_escolaridade'
        )
        ->where('esc.fk_id_cargo', $idCargo)
        ->get()
        ->getResult();
    }
}