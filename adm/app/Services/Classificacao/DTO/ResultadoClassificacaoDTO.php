<?php

namespace App\Services\Classificacao\DTO;

/**
 * DTO responsável por transportar os dados
 * consolidados de classificação de um candidato.
 */
class ResultadoClassificacaoDTO
{
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
        public float $nr_total_pontos,
        public int $ds_possui_pne = 0,
    ) {}
}