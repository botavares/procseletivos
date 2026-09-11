<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassificacaoModel extends Model
{
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

    protected $validationRules = [];
    protected $returnType = 'object';

    /**
     * Lista classificacao com scores dinamicos.
     */
    public function listarClassificacao($idEdital, $idCargo)
    {
        $classificacoes = $this->db->table($this->table)
            ->select('*')
            ->where('fk_id_edital', $idEdital)
            ->where('fk_id_cargo', $idCargo)
            ->orderBy('ds_posicao', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($classificacoes)) {
            return [];
        }

        // Busca os scores dinamicos
        $idsClassificacao = array_column($classificacoes, 'pk_id_classificacao');

        $scores = $this->db->table('tb_classificacao_scores')
            ->whereIn('fk_id_classificacao', $idsClassificacao)
            ->orderBy('pk_id_score', 'ASC')
            ->get()
            ->getResultArray();

        // Organiza scores por classificacao
        $scoresPorClassificacao = [];
        foreach ($scores as $score) {
            $idClass = $score['fk_id_classificacao'];
            if (!isset($scoresPorClassificacao[$idClass])) {
                $scoresPorClassificacao[$idClass] = [];
            }
            $scoresPorClassificacao[$idClass][$score['ds_chave_score']] = [
                'nr_valor'       => $score['nr_valor'],
                'ds_valor_texto' => $score['ds_valor_texto'],
                'ds_label'       => $score['ds_label'],
            ];
        }

        // Anexa scores aos resultados
        foreach ($classificacoes as &$classificacao) {
            $idClass = $classificacao['pk_id_classificacao'];
            $classificacao['_scores'] = $scoresPorClassificacao[$idClass] ?? [];
        }

        return $classificacoes;
    }
}
