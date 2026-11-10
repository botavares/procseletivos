<?php

declare(strict_types=1);

namespace App\Services\Transparencia;

use App\Presenters\TransparenciaPresenter;
use App\Repositories\Interfaces\ITransparenciaRepository;
use App\Services\Classificacao\DesempateConfigService;
use App\Services\Interfaces\ITransparenciaService;

class TransparenciaService implements ITransparenciaService
{
    public function __construct(
        private ITransparenciaRepository $transparenciaRepository,
        private TransparenciaPresenter $presenter,
        private DesempateConfigService $desempateConfigService,
    ) {}

    public function listarClassificacao(array $params): array
    {
        $pagina    = max(1, (int) ($params['page'] ?? 1));
        $porPagina = max(1, (int) ($params['per_page'] ?? 10));
        $cargo     = (int) ($params['cargo'] ?? 0);
        $processo  = (int) ($params['processo'] ?? 0);
        $busca     = trim((string) ($params['search'] ?? ''));

        $usaDesempateDinamico = false;
        $colunasDinamicas = [];
        if ($cargo > 0) {
            $usaDesempateDinamico = $this->desempateConfigService->cargoPossuiConfiguracao($cargo, $processo > 0 ? $processo : null, null);
            if ($usaDesempateDinamico) {
                $colunasDinamicas = $this->montarColunasDinamicas($cargo, $busca, $processo > 0 ? $processo : null);
            }
        }

        $colunasOcultas = [];
        $criteriosAdicionais = [];
        if (!$usaDesempateDinamico) {
            $colunasOcultas = $this->transparenciaRepository->obterColunasOcultas($cargo, $busca, $processo > 0 ? $processo : null);
            $criteriosAdicionais = $this->transparenciaRepository->obterCriteriosAdicionaisAtivos($cargo, $busca, $processo > 0 ? $processo : null);
        }

        $resultado = $this->transparenciaRepository->listarClassificacao(
            $pagina,
            $porPagina,
            $cargo,
            $busca,
            $usaDesempateDinamico,
            $colunasDinamicas,
            $criteriosAdicionais,
            $processo > 0 ? $processo : null
        );

        $dados = $this->presenter->processarSituacaoCandidatos($resultado['dados']);

        return [
            'dados' => $dados,
            'paginacao' => $this->presenter->calcularPaginacao($pagina, $porPagina, $resultado['total']),
            'filtros' => $params,
            'total' => $resultado['total'],
            'colunas_ocultas' => $colunasOcultas,
            'criterios_adicionais' => $criteriosAdicionais,
            'usa_desempate_dinamico' => $usaDesempateDinamico,
            'colunas_dinamicas' => $colunasDinamicas,
        ];
    }

    private function montarColunasDinamicas(int $cargo, string $busca, ?int $idProcesso = null): array
    {
        $rows = $this->transparenciaRepository->buscarColunasDinamicas($cargo, $busca, $idProcesso);
        $meta = $this->presenter->resolverLabelsColunasDinamicas($rows, $cargo, $idProcesso);
        $labelsConfig = $meta['labels'];
        $tiposCriterio = $meta['tipos'];
        $nomesReferencia = $this->transparenciaRepository->carregarNomesReferencias($cargo);

        $colunas = [];
        foreach ($rows as $row) {
            $chave = $row->ds_chave_score;
            $tipo = $tiposCriterio[$chave] ?? null;

            if ($tipo === 'IDADE' || $tipo === 'PNE') {
                continue;
            }

            $label = $row->ds_label ?? null;

            if (!$label || $label === $chave || str_starts_with($label, 'EXPERIENCIA_') || str_starts_with($label, 'ESCOLARIDADE_') || str_starts_with($label, 'CRITERIO_') || str_starts_with($label, 'APERFEICOAMENTO_') || str_starts_with($label, 'PONTUACAO_')) {
                if (isset($labelsConfig[$chave])) {
                    $label = $labelsConfig[$chave];
                } else {
                    $label = $this->presenter->resolverLabelPorChave($chave, $nomesReferencia);
                }
            }

            $colunas[] = [
                'chave' => $chave,
                'alias' => 'score_' . md5($chave),
                'label' => $label ?: $chave,
            ];
        }

        return $colunas;
    }
}
