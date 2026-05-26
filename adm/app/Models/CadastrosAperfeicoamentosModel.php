<?php
namespace App\Models;

use CodeIgniter\Model;

class CadastrosAperfeicoamentosModel extends Model{
    //Atributos
    protected $table = 'tb_cadastrados_aperfeicoamentos';
    protected $primaryKey = 'fk_id_cadastrado';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'fk_id_edital', 
        'fk_id_cargo', 
        'fk_id_curso',
        'ds_status',
        'ds_quantidade',
        'ds_multiplicador',
    ];

    protected $returnType = 'object';

    public function buscarAperfeicoamentosParaClassificacao($cadastrado, $edital, $cargo )
    {
        return $this->select([
                'tb_cadastrados_aperfeicoamentos.fk_id_cadastrado',
                'tb_cadastrados_aperfeicoamentos.fk_id_edital',
                'tb_cadastrados_aperfeicoamentos.fk_id_curso',
                'tb_cadastrados_aperfeicoamentos.ds_status',
                'tb_cadastrados_aperfeicoamentos.ds_quantidade',
                'tb_cadastrados_aperfeicoamentos.ds_multiplicador',
                'tb_cursos_aperfeicoamentos.ds_nome_curso',
                'tb_cargos_aperfeicoamentos_editais.ds_pontuacao_maxima'
            ])
            ->join(
                'tb_cargos_aperfeicoamentos_editais',
                'tb_cadastrados_aperfeicoamentos.fk_id_cargo = tb_cargos_aperfeicoamentos_editais.fk_id_cargo
                 AND tb_cadastrados_aperfeicoamentos.fk_id_curso = tb_cargos_aperfeicoamentos_editais.fk_id_curso'
            )
            ->join(
                'tb_cursos_aperfeicoamentos',
                'tb_cargos_aperfeicoamentos_editais.fk_id_curso = tb_cursos_aperfeicoamentos.pk_id_curso'
            )
            ->where([
                'tb_cadastrados_aperfeicoamentos.fk_id_edital'     => $edital,
                'tb_cadastrados_aperfeicoamentos.fk_id_cargo'      => $cargo,
                'tb_cadastrados_aperfeicoamentos.fk_id_cadastrado' => $cadastrado,
            ])
            ->findAll();
    }
    public function listarAperfeicoamentos($idCargo){
        return $this->db->table('tb_cargos_aperfeicoamentos_editais aper')
        ->select("
            aper.fk_id_curso,
            c.ds_nome_curso,
            aper.ds_multiplicador,
            aper.ds_pontuacao_minima,
            aper.ds_pontuacao_maxima,
            aper.ds_tipo_campo,
            
        ")
        ->join(
            'tb_cursos_aperfeicoamentos c',
            'c.pk_id_curso = aper.fk_id_curso'
        )
        ->where('aper.fk_id_cargo', $idCargo)
        ->get()
        ->getResult();
    }

}