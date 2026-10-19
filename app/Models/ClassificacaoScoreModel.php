<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ClassificacaoScoreModel extends Model
{
    protected $table = 'tb_classificacao_scores';
    protected $primaryKey = 'pk_id_score';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'fk_id_classificacao',
        'ds_tipo_score',
        'ds_chave_score',
        'ds_label',
        'nr_valor',
        'ds_valor_texto',
    ];

    protected $validationRules = [
        'fk_id_classificacao' => 'required|integer',
        'ds_tipo_score' => 'required|string',
        'ds_chave_score' => 'required|string',
        'ds_label' => 'permit_empty|string',
        'nr_valor' => 'permit_empty|numeric',
        'ds_valor_texto' => 'permit_empty|string',
    ];

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
