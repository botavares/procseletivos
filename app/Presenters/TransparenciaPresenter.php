<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Services\Classificacao\DesempateConfigService;

class TransparenciaPresenter
{
    public function __construct(private DesempateConfigService $desempateConfigService) {}

    /**
     * Formata o código da situação para texto legível.
     */
    public function formatarSituacao(mixed $situacao): string
    {
        $valor = is_numeric($situacao) ? (int) $situacao : null;

        return match ($valor) {
            1 => 'Convocado',
            2 => 'Contratado',
            3 => 'Eliminado',
            default => 'Aguardando',
        };
    }

    /**
     * Processa a situação de cada candidato e adiciona prefixo PCD se necessário
     */
    public function processarSituacaoCandidatos(array $dados): array
    {
        foreach ($dados as $candidato) {
            $situacaoFormatada = $this->formatarSituacao($candidato->situacao ?? null);

            $pne = (int) ($candidato->ds_possui_pne ?? 0);
            if ($pne === 1) {
                $candidato->ds_situacao = 'PCD - ' . $situacaoFormatada;
            } else {
                $candidato->ds_situacao = $situacaoFormatada;
            }
        }

        return $dados;
    }

    /**
     * Calcula paginação
     */
    public function calcularPaginacao(
        int $pagina,
        int $porPagina,
        int $total,
        int $limite = 5
    ): array {
        $totalPaginas = $total > 0 ? (int) ceil($total / $porPagina) : 1;
        $pagina = max(1, min($pagina, $totalPaginas));
        $metade = intdiv($limite, 2);
        $inicio = max(1, $pagina - $metade);
        $fim    = min($totalPaginas, $inicio + $limite - 1);
        $inicio = max(1, $fim - $limite + 1);

        return [
            'paginaAtual' => $pagina,
            'porPagina'   => $porPagina,
            'total'       => $total,
            'totalPaginas'=> $totalPaginas,
            'inicio'      => $inicio,
            'fim'         => $fim,
            'temAnterior' => $pagina > 1,
            'temProxima'  => $pagina < $totalPaginas,
        ];
    }

    /**
     * Resolve labels legíveis para colunas dinâmicas
     */
    public function resolverLabelsColunasDinamicas(array $rows, int $cargo): array
    {
        $configDesempate = $this->desempateConfigService->buscarConfiguracao($cargo);
        $labelsConfig = [];
        $tiposCriterio = [];

        foreach ($configDesempate as $cfg) {
            $labelsConfig[$cfg->chaveScore()] = $cfg->descricao;
            $tiposCriterio[$cfg->chaveScore()] = $cfg->tipoCriterio;
        }

        return [
            'labels' => $labelsConfig,
            'tipos'  => $tiposCriterio,
        ];
    }

    /**
     * Resolve um label legível a partir da chave técnica.
     */
    public function resolverLabelPorChave(string $chave, array $nomesReferencia): string
    {
        if (str_starts_with($chave, 'EXPERIENCIA_ESPECIFICA_')) {
            $id = (int) substr($chave, strlen('EXPERIENCIA_ESPECIFICA_'));
            return $nomesReferencia['experiencias'][$id] ?? "Experiência #{$id}";
        }

        if (str_starts_with($chave, 'ESCOLARIDADE_ESPECIFICA_')) {
            $id = (int) substr($chave, strlen('ESCOLARIDADE_ESPECIFICA_'));
            return $nomesReferencia['escolaridades'][$id] ?? "Escolaridade #{$id}";
        }

        if (str_starts_with($chave, 'CRITERIO_ADICIONAL_')) {
            $id = (int) substr($chave, strlen('CRITERIO_ADICIONAL_'));
            return $nomesReferencia['criterios'][$id] ?? "Critério #{$id}";
        }

        if (str_starts_with($chave, 'APERFEICOAMENTO_ESPECIFICO_')) {
            $id = (int) substr($chave, strlen('APERFEICOAMENTO_ESPECIFICO_'));
            return $nomesReferencia['cursos'][$id] ?? "Aperfeiçoamento #{$id}";
        }

        $mapaLabels = [
            'PONTUACAO_TOTAL' => 'Pontuação Total',
            'PONTUACAO_EXPERIENCIAS' => 'Total Experiências',
            'PONTUACAO_ESCOLARIDADES' => 'Total Escolaridades',
            'PONTUACAO_CRITERIOS_ADICIONAIS' => 'Total Critérios Adicionais',
            'PONTUACAO_CURSOS_APERFEICOAMENTOS' => 'Total Cursos',
            'PONTUACAO_GRADUACAO' => 'Total Graduação',
            'PONTUACAO_POS_GRADUACAO' => 'Total Pós-Graduação',
            'PONTUACAO_MESTRADO' => 'Total Mestrado',
            'PONTUACAO_DOUTORADO' => 'Total Doutorado',
            'PONTUACAO_APERFEICOAMENTOS' => 'Total Aperfeiçoamentos',
            'IDADE' => 'Idade',
            'PNE' => 'PNE',
        ];

        return $mapaLabels[$chave] ?? $chave;
    }
}
