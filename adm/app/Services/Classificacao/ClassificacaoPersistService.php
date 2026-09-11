<?php

namespace App\Services\Classificacao;

use CodeIgniter\Database\BaseConnection;
use App\Models\ClassificacaoScoreModel;

/**
 * Responsavel por persistir a classificacao
 * utilizando transacao e insertBatch (backward compat)
 * ou insert individual quando ha scores dinamicos.
 */
class ClassificacaoPersistService
{
    public function __construct(
        private BaseConnection $db
    ) {}

    /**
     * Salva a classificacao com suporte a scores dinamicos.
     *
     * @param int $edital
     * @param int $cargo
     * @param array $dados Cada item pode ter '_scores' com scores dinamicos
     */
    public function salvar(int $edital, int $cargo, array $dados): void
    {
        if (empty($dados)) {
            return;
        }

        // Verifica se ha scores dinamicos
        $usaScoresDinamicos = false;
        foreach ($dados as $row) {
            if (isset($row['_scores']) && !empty($row['_scores'])) {
                $usaScoresDinamicos = true;
                break;
            }
        }

        $this->db->transStart();

        // Limpa classificacao anterior (CASCADE limpa scores automaticamente)
        $this->db->table('tb_classificacao')
            ->where([
                'fk_id_edital' => $edital,
                'fk_id_cargo'  => $cargo
            ])
            ->delete();

        if ($usaScoresDinamicos) {
            $this->salvarComScores($edital, $cargo, $dados);
        } else {
            $this->salvarBatch($edital, $cargo, $dados);
        }

        $this->db->transComplete();
    }

    /**
     * Salva usando insertBatch (performance para regra fixa).
     */
    private function salvarBatch(int $edital, int $cargo, array $dados): void
    {
        foreach ($dados as &$row) {
            $row['fk_id_edital'] = $edital;
            $row['fk_id_cargo']  = $cargo;
            // Remove scores se existirem (nao deveria, mas garante)
            unset($row['_scores']);
        }

        $this->db->table('tb_classificacao')->insertBatch($dados);
    }

    /**
     * Salva com insert individual para obter IDs e persistir scores dinamicos.
     */
    private function salvarComScores(int $edital, int $cargo, array $dados): void
    {
        $scoreModel = new ClassificacaoScoreModel();

        foreach ($dados as $row) {
            $scores = $row['_scores'] ?? [];
            unset($row['_scores']);

            $row['fk_id_edital'] = $edital;
            $row['fk_id_cargo']  = $cargo;

            $this->db->table('tb_classificacao')->insert($row);
            $idClassificacao = $this->db->insertID();

            if ($idClassificacao && !empty($scores)) {
                $batchScores = [];
                foreach ($scores as $score) {
                    $batchScores[] = [
                        'fk_id_classificacao' => $idClassificacao,
                        'ds_tipo_score'       => $this->inferirTipoScore($score['ds_chave_score']),
                        'ds_chave_score'      => $score['ds_chave_score'],
                        'ds_label'            => $score['ds_label'] ?? $score['ds_chave_score'],
                        'nr_valor'            => $score['nr_valor'],
                        'ds_valor_texto'      => $score['ds_valor_texto'] ?? null,
                    ];
                }
                $scoreModel->inserirBatch($batchScores);
            }
        }
    }

    /**
     * Infere o tipo de score a partir da chave.
     */
    private function inferirTipoScore(string $chave): string
    {
        if (str_starts_with($chave, 'PONTUACAO_')) {
            return 'PONTUACAO';
        }
        if (str_starts_with($chave, 'EXPERIENCIA_ESPECIFICA_')) {
            return 'EXPERIENCIA_ESPECIFICA';
        }
        if (str_starts_with($chave, 'ESCOLARIDADE_ESPECIFICA_')) {
            return 'ESCOLARIDADE_ESPECIFICA';
        }
        if (str_starts_with($chave, 'CRITERIO_ADICIONAL_')) {
            return 'CRITERIO_ADICIONAL';
        }
        if (str_starts_with($chave, 'APERFEICOAMENTO_ESPECIFICO_')) {
            return 'APERFEICOAMENTO_ESPECIFICO';
        }
        if ($chave === 'IDADE') {
            return 'IDADE';
        }
        if ($chave === 'PNE') {
            return 'PNE';
        }
        return 'OUTRO';
    }
}
