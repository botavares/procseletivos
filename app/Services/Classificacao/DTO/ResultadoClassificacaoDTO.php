<?php

namespace App\Services\Classificacao\DTO;

class ResultadoClassificacaoDTO
{
    // Campos existentes (mantidos para compatibilidade)
    public int $fk_id_candidato;
    public string $ds_nome;
    public string $ds_nome_cargo;
    public string $ds_nome_edital;
    public string $ds_nascimento;
    public float $nr_total_experiencias = 0;
    public float $nr_total_graduacao = 0;
    public float $nr_total_posgraduacao = 0;
    public float $nr_total_mestrado = 0;
    public float $nr_total_doutorado = 0;
    public float $nr_total_aperfeicoamentos = 0;
    public float $nr_total_pontos = 0;
    public int $ds_possui_pne = 0;
    public int $ds_posicao = 0;

    // NOVO: Mapa de scores dinamicos para desempate
    public array $scores = [];

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
