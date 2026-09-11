<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassificacaoScoreModel extends Model
{
    protected $table = 'tb_classificacao_scores';
    protected $primaryKey = 'pk_id_score';
    protected $allowedFields = [
        'fk_id_classificacao',
        'ds_tipo_score',
        'ds_chave_score',
        'ds_label',
        'nr_valor',
        'ds_valor_texto',
    ];
    protected $returnType = 'object';

    /**
     * Lista scores por classificacao.
     *
     * @param int $idClassificacao
     * @return array
     */
    public function listarPorClassificacao(int $idClassificacao): array
    {
        return $this->where('fk_id_classificacao', $idClassificacao)
                    ->orderBy('pk_id_score', 'ASC')
                    ->findAll();
    }

    /**
     * Lista todos os scores de um edital/cargo.
     *
     * @param int $edital
     * @param int $cargo
     * @return array
     */
    public function listarPorEditalCargo(int $edital, int $cargo): array
    {
        return $this->db->table('tb_classificacao_scores s')
            ->select('s.*')
            ->join('tb_classificacao c', 'c.pk_id_classificacao = s.fk_id_classificacao')
            ->where('c.fk_id_edital', $edital)
            ->where('c.fk_id_cargo', $cargo)
            ->orderBy('s.pk_id_score', 'ASC')
            ->get()
            ->getResult();
    }

    /**
     * Insere batch de scores.
     *
     * @param array $scores
     * @return bool
     */
    public function inserirBatch(array $scores): bool
    {
        if (empty($scores)) {
            return true;
        }

        return $this->insertBatch($scores);
    }
}
