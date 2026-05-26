<?php

namespace App\Services\Classificacao;

use CodeIgniter\Database\BaseConnection;
use App\Services\Classificacao\DTO\ResultadoClassificacaoDTO;
use App\Services\Editais\EditalService;
use App\Traits\FormatarNumeroEditalTrait;


/**
 * Processa todos os candidatos
 * e monta array final pronto para persistência.
 */
class ClassificacaoProcessorService
{
    use FormatarNumeroEditalTrait;
    public function __construct(
        private BaseConnection $db,
        private PontuacaoCalculatorService $calculator
    ) {}

    /**
     * Processa classificação completa.
     */
    public function processar(int $edital, int $cargo): array
    {
        $candidatos = $this->buscarCandidatos($edital, $cargo);
        $editalService = new EditalService();
        $dadosEdital = $editalService->listarEditalId($edital);
        $nomeEdital = $this->formatarNumeroEdital($dadosEdital->ds_numero_edital);
        
        $resultados = [];

        foreach ($candidatos as $candidato) {

            $pontuacoes = $this->calculator->calcular(
                $candidato->fk_id_cadastrado,
                $edital,
                $cargo
            );

            $totalGeral = array_sum($pontuacoes);

            // Define se possui PNE (fk_id_pne > 1 indica deficiência)
            $dsPossuiPne = ($candidato->fk_id_pne > 1) ? 1 : 0;

            $resultados[] = new ResultadoClassificacaoDTO(
                $candidato->fk_id_cadastrado,
                $candidato->ds_nome,
                $candidato->ds_nome_cargo,
                $nomeEdital,
                $candidato->ds_nascimento,
                $pontuacoes['experiencias'],
                $pontuacoes['graduacao'],
                $pontuacoes['posgraduacao'],
                $pontuacoes['mestrado'],
                $pontuacoes['doutorado'],
                $pontuacoes['aperfeicoamentos'],
                $totalGeral,
                $dsPossuiPne
            );
        }
        
        return $this->ordenarEClassificar($resultados);
    }

    /**
     * Busca todos os candidatos do edital/cargo.
     * Apenas os necessários.
     */
    private function buscarCandidatos(int $edital, int $cargo)
    {
        return $this->db->table('tb_cadastrados_protocolo p')
            ->select('
            p.fk_id_cadastrado,
            c.ds_nome,
            c.ds_nascimento,
            c.fk_id_pne,
            cg.ds_nome_cargo
        ')
        ->join('tb_cadastrados c', 'c.pk_id_cadastrado = p.fk_id_cadastrado')
        ->join('tb_cargos cg', 'cg.pk_id_cargo = p.fk_id_cargo')
        ->where([
            'p.fk_id_edital' => $edital,
            'p.fk_id_cargo'  => $cargo
        ])
        ->get()
        ->getResult();
    }

    /**
     * Ordena por pontuação e gera posição.
     */
    private function ordenarEClassificar(array $resultados): array{
        usort($resultados, function ($a, $b) {
            return 
                $b->nr_total_pontos <=> $a->nr_total_pontos
                ?: $b->nr_total_experiencias <=> $a->nr_total_experiencias
                ?: $b->nr_total_graduacao <=> $a->nr_total_graduacao
                ?: $b->nr_total_posgraduacao <=> $a->nr_total_posgraduacao
                ?: strtotime($a->ds_nascimento) <=> strtotime($b->ds_nascimento);
            });

        $dados = [];
        $posicao = 1;
        
        foreach ($resultados as $r) {

            $dados[] = [
                'ds_posicao'                => $posicao++,
                'fk_id_edital'              => null,
                'fk_id_cargo'               => null,
                'ds_nome_cargo'             => $r->ds_nome_cargo,
                'fk_id_candidato'           => $r->fk_id_candidato,
                'ds_nome_candidato'         => $r->ds_nome,
                'ds_nome_edital'            => $r->ds_nome_edital,
                'nr_total_pontos'           => $r->nr_total_pontos,
                'nr_total_experiencias'     => $r->nr_total_experiencias,
                'nr_total_graduacao'        => $r->nr_total_graduacao,
                'nr_total_posgraduacao'     => $r->nr_total_posgraduacao,
                'nr_total_mestrado'         => $r->nr_total_mestrado,
                'nr_total_doutorado'        => $r->nr_total_doutorado,
                'nr_total_aperfeicoamentos' => $r->nr_total_aperfeicoamentos,
                'dt_nascimento'             => $r->ds_nascimento,
                'ds_possui_pne'             => $r->ds_possui_pne,
                'dt_processamento'          => date('Y-m-d H:i:s')
            ];
        }
        return $dados;
    }
}