<?php

namespace App\Services\Classificacao\DTO;

/**
 * DTO responsavel por transportar os dados
 * consolidados de classificacao de um candidato.
 */
class ResultadoClassificacaoDTO
{
    /**
     * Mapa de scores dinamicos para desempate.
     * Formato: ['PONTUACAO_TOTAL' => 50.0, 'EXPERIENCIA_ESPECIFICA_7' => 25.0]
     */
    public array $scores = [];

    /**
     * Mapa de labels para os scores.
     * Formato: ['PONTUACAO_TOTAL' => 'Pontuacao Total', 'EXPERIENCIA_ESPECIFICA_7' => 'Experiencia GIS']
     */
    public array $scoreLabels = [];

    public function __construct(
        public int $fk_id_candidato,
        public string $ds_nome,
        public string $ds_nome_cargo,
        public string $ds_nome_edital,
        public string $ds_nascimento,
        public float $nr_total_experiencias,
        public float $nr_total_graduacao,
        public float $nr_total_posgraduacao,
        public float $nr_total_mestrado,
        public float $nr_total_doutorado,
        public float $nr_total_aperfeicoamentos,
        public float $nr_total_criterios_adicionais,
        public float $nr_total_pontos,
        public int $ds_possui_pne = 0,
    ) {}

    /**
     * Converte os scores em array para persistencia.
     *
     * @return array
     */
    public function scoresParaPersistir(): array
    {
        $persistidos = [];
        foreach ($this->scores as $chave => $valor) {
            $persistidos[] = [
                'ds_chave_score' => $chave,
                'nr_valor'       => is_numeric($valor) ? (float) $valor : 0,
                'ds_valor_texto' => is_string($valor) ? $valor : null,
            ];
        }
        return $persistidos;
    }
}
