<?php
namespace App\Models;

use CodeIgniter\Model;

class ClassificacaoModel extends Model{
    //Atributos
    protected $table = 'tb_classificacao';
    protected $primaryKey = 'pk_id_classificacao';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'ds_posicao',
        'fk_id_edital',
        'fk_id_cargo',
        'ds_nome_cargo',
        'ds_nome_edital',
        'fk_id_candidato',
        'ds_nome_candidato',
        'nr_total_pontos',
        'nr_total_experiencias',
        'nr_total_graduacao',
        'nr_total_pos_graduacao',
        'nr_total_mestrado',
        'nr_total_doutorado',
        'nr_total_aperfeicoamentos',
        'dt_nascimento',
        'dt_processamento',
        'ds_possui_pne'
    ];

    protected $validationRules = [
        
        
    ];

    protected $returnType = 'object';

    public function listarClassificacao($idEdital, $idCargo){
        return $this->db->table($this->table)
            ->select('*')
            ->where('fk_id_edital', $idEdital)
            ->where('fk_id_cargo', $idCargo)
            ->orderBy('ds_posicao', 'ASC')
            ->get()
            ->getResultArray();
    }
}