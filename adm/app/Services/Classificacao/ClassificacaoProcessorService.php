<?php

namespace App\Services\Classificacao;

use CodeIgniter\Database\BaseConnection;
use App\Services\Classificacao\DTO\ResultadoClassificacaoDTO;
use App\Services\Editais\EditalService;
use App\Traits\FormatarNumeroEditalTrait;

/**
 * Processa todos os candidatos
 * e monta array final pronto para persistencia.
 *
 * Agora com suporte a desempate dinamico configuravel por cargo.
 * Mantem backward compatibility: cargos sem configuracao usam a regra fixa.
 */
class ClassificacaoProcessorService
{
    use FormatarNumeroEditalTrait;

    private DesempateConfigService $desempateConfigService;
    private OrdenacaoDinamicaService $ordenacaoDinamicaService;

    public function __construct(
        private BaseConnection $db,
        private PontuacaoCalculatorService $calculator
    ) {
        $this->desempateConfigService = new DesempateConfigService();
        $this->ordenacaoDinamicaService = new OrdenacaoDinamicaService();
    }

    /**
     * Processa classificacao completa com suporte a desempate dinamico.
     *
     * @param int $edital
     * @param int $cargo
     * @return array Array de arrays com dados + scores
     */
    public function processar(int $edital, int $cargo): array
    {
        $configDesempate = $this->desempateConfigService->buscarConfiguracao($cargo);
        $usaConfiguracaoDinamica = !empty($configDesempate);

        $candidatos = $this->buscarCandidatos($edital, $cargo);
        $editalService = new EditalService();
        $dadosEdital = $editalService->listarEditalId($edital);
        $nomeEdital = $this->formatarNumeroEdital($dadosEdital->ds_numero_edital);

        // Prepara lista de IDs para carregamento batch (quando dinamico)
        $idsCandidatos = array_column($candidatos, 'fk_id_cadastrado');

        $resultadosDTO = [];

        if ($usaConfiguracaoDinamica) {
            // Carrega dados em batch para performance
            $scoreBuilder = new CandidatoScoreBuilderService($this->db);
            $scoreBuilder->carregarDadosBatch($idsCandidatos, $edital, $cargo, $configDesempate);

            foreach ($candidatos as $candidato) {
                $pontuacoes = $this->calculator->calcular(
                    (int) $candidato['fk_id_cadastrado'],
                    $edital,
                    $cargo
                );

                $totalGeral = array_sum($pontuacoes);
                $dsPossuiPne = ((int) ($candidato['fk_id_pne'] ?? 0)) > 1 ? 1 : 0;

                $dto = new ResultadoClassificacaoDTO(
                    (int) $candidato['fk_id_cadastrado'],
                    $candidato['ds_nome'],
                    $candidato['ds_nome_cargo'],
                    $nomeEdital,
                    $candidato['ds_nascimento'],
                    $pontuacoes['experiencias'] ?? 0,
                    $pontuacoes['graduacao'] ?? 0,
                    $pontuacoes['posgraduacao'] ?? 0,
                    $pontuacoes['mestrado'] ?? 0,
                    $pontuacoes['doutorado'] ?? 0,
                    $pontuacoes['aperfeicoamentos'] ?? 0,
                    $pontuacoes['criterios_adicionais'] ?? 0,
                    $totalGeral,
                    $dsPossuiPne
                );

                // Calcula scores dinamicos
                $dto->scores = $scoreBuilder->construirScores($dto->fk_id_candidato, $configDesempate);

                // Sobrescreve scores de totais agregados com valores ja calculados
                // (evita recalcular no scoreBuilder e usar valores incorretos)
                foreach ($configDesempate as $cfg) {
                    $chave = $cfg->chaveScore();
                    switch ($cfg->tipoCriterio) {
                        case 'PONTUACAO_TOTAL':
                            $dto->scores[$chave] = $dto->nr_total_pontos;
                            break;
                        case 'PONTUACAO_EXPERIENCIAS':
                            $dto->scores[$chave] = $dto->nr_total_experiencias;
                            break;
                        case 'PONTUACAO_ESCOLARIDADES':
                            // Calcula soma de todos os niveis
                            $dto->scores[$chave] = $dto->nr_total_graduacao + $dto->nr_total_posgraduacao + $dto->nr_total_mestrado + $dto->nr_total_doutorado;
                            break;
                        case 'PONTUACAO_CRITERIOS_ADICIONAIS':
                            $dto->scores[$chave] = $dto->nr_total_criterios_adicionais;
                            break;
                        case 'PONTUACAO_CURSOS_APERFEICOAMENTOS':
                        case 'PONTUACAO_APERFEICOAMENTOS':
                            $dto->scores[$chave] = $dto->nr_total_aperfeicoamentos;
                            break;
                        case 'PONTUACAO_GRADUACAO':
                            $dto->scores[$chave] = $dto->nr_total_graduacao;
                            break;
                        case 'PONTUACAO_POS_GRADUACAO':
                            $dto->scores[$chave] = $dto->nr_total_posgraduacao;
                            break;
                        case 'PONTUACAO_MESTRADO':
                            $dto->scores[$chave] = $dto->nr_total_mestrado;
                            break;
                        case 'PONTUACAO_DOUTORADO':
                            $dto->scores[$chave] = $dto->nr_total_doutorado;
                            break;
                        case 'IDADE':
                            $dto->scores[$chave] = $dto->ds_nascimento;
                            break;
                        case 'PNE':
                            $dto->scores[$chave] = $dto->ds_possui_pne;
                            break;
                    }
                }

                // Armazena labels dos scores para persistencia
                foreach ($configDesempate as $cfg) {
                    $dto->scoreLabels[$cfg->chaveScore()] = $cfg->descricao;
                }

                $resultadosDTO[] = $dto;
            }

            // Ordenacao dinamica
            $resultadosDTO = $this->ordenacaoDinamicaService->ordenar($resultadosDTO, $configDesempate);

            return $this->converterParaArray($resultadosDTO, true, $configDesempate);
        }

        // ======================================
        // BACKWARD COMPATIBILITY: Regra fixa
        // ======================================
        foreach ($candidatos as $candidato) {
            $pontuacoes = $this->calculator->calcular(
                (int) $candidato['fk_id_cadastrado'],
                $edital,
                $cargo
            );

            $totalGeral = array_sum($pontuacoes);
            $dsPossuiPne = ((int) ($candidato['fk_id_pne'] ?? 0)) > 1 ? 1 : 0;

            $dto = new ResultadoClassificacaoDTO(
                (int) $candidato['fk_id_cadastrado'],
                $candidato['ds_nome'],
                $candidato['ds_nome_cargo'],
                $nomeEdital,
                $candidato['ds_nascimento'],
                $pontuacoes['experiencias'] ?? 0,
                $pontuacoes['graduacao'] ?? 0,
                $pontuacoes['posgraduacao'] ?? 0,
                $pontuacoes['mestrado'] ?? 0,
                $pontuacoes['doutorado'] ?? 0,
                $pontuacoes['aperfeicoamentos'] ?? 0,
                $pontuacoes['criterios_adicionais'] ?? 0,
                $totalGeral,
                $dsPossuiPne
            );

            $resultadosDTO[] = $dto;
        }

        return $this->ordenarEClassificar($resultadosDTO);
    }

    /**
     * Busca todos os candidatos do edital/cargo.
     */
    private function buscarCandidatos(int $edital, int $cargo): array
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
            ->getResultArray();
    }

    /**
     * Ordena por pontuacao e gera posicao (regra fixa - backward compatibility).
     */
    private function ordenarEClassificar(array $resultados): array
    {
        usort($resultados, function (ResultadoClassificacaoDTO $a, ResultadoClassificacaoDTO $b) {
            return
                $b->nr_total_pontos <=> $a->nr_total_pontos
                ?: $b->nr_total_experiencias <=> $a->nr_total_experiencias
                ?: $b->nr_total_doutorado <=> $a->nr_total_doutorado
                ?: $b->nr_total_mestrado <=> $a->nr_total_mestrado
                ?: $b->nr_total_posgraduacao <=> $a->nr_total_posgraduacao
                ?: strtotime($a->ds_nascimento) <=> strtotime($b->ds_nascimento);
        });

        return $this->converterParaArray($resultados, false);
    }

    /**
     * Converte array de DTOs para array de persistencia.
     *
     * @param ResultadoClassificacaoDTO[] $resultados
     * @param bool $incluirScores
     * @return array
     */
    private function converterParaArray(array $resultados, bool $incluirScores): array
    {
        $dados = [];
        $posicao = 1;

        foreach ($resultados as $r) {
            $item = [
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
                'nr_total_criterios_adicionais' => $r->nr_total_criterios_adicionais,
                'dt_nascimento'             => $r->ds_nascimento,
                'ds_possui_pne'             => $r->ds_possui_pne,
                'dt_processamento'          => date('Y-m-d H:i:s'),
            ];

            if ($incluirScores && !empty($r->scores)) {
                $item['_scores'] = [];
                foreach ($r->scores as $chave => $valor) {
                    $item['_scores'][] = [
                        'ds_chave_score' => $chave,
                        'ds_label'       => $r->scoreLabels[$chave] ?? $chave,
                        'nr_valor'       => is_numeric($valor) ? (float) $valor : 0,
                        'ds_valor_texto' => is_string($valor) ? $valor : null,
                    ];
                }
            }

            $dados[] = $item;
        }

        return $dados;
    }
}
