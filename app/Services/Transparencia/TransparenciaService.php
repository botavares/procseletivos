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
        $edital    = (int) ($params['edital'] ?? 0);
        $busca     = trim((string) ($params['search'] ?? ''));

        $editaisAtivos = [];
        if ($edital <= 0) {
            $editaisAtivos = $this->transparenciaRepository->buscarEditaisAtivos();
        }

        $idProcesso = $this->resolverProcessoPorEdital($edital);

        $usaDesempateDinamico = false;
        $colunasDinamicas = [];
        if ($cargo > 0) {
            $usaDesempateDinamico = $this->desempateConfigService->cargoPossuiConfiguracao($cargo, $idProcesso);
            if ($usaDesempateDinamico) {
                $colunasDinamicas = $this->montarColunasDinamicas($cargo, $edital, $busca, $editaisAtivos, $idProcesso);
            }
        }

        $colunasOcultas = [];
        $criteriosAdicionais = [];
        if (!$usaDesempateDinamico) {
            $colunasOcultas = $this->transparenciaRepository->obterColunasOcultas($cargo, $edital, $busca, $editaisAtivos);
            $criteriosAdicionais = $this->transparenciaRepository->obterCriteriosAdicionaisAtivos($cargo, $edital, $busca, $editaisAtivos);
        }

        $resultado = $this->transparenciaRepository->listarClassificacao(
            $pagina,
            $porPagina,
            $cargo,
            $edital,
            $busca,
            $editaisAtivos,
            $usaDesempateDinamico,
            $colunasDinamicas,
            $criteriosAdicionais
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

    /**
     * Resolve o processo seletivo vinculado ao edital pelo numero/ano.
     */
    private function resolverProcessoPorEdital(int $edital): ?int
    {
        $db = \Config\Database::connect();

        $editalRow = $db->table('tb_editais')
            ->select('ds_numero_edital')
            ->where('pk_id_edital', $edital)
            ->get()
            ->getRow();

        if (empty($editalRow->ds_numero_edital)) {
            return null;
        }

        $numeroCompleto = (string) $editalRow->ds_numero_edital;
        if (strlen($numeroCompleto) < 5) {
            return null;
        }

        $ano    = (int) substr($numeroCompleto, -4);
        $numero = (int) substr($numeroCompleto, 0, -4);

        $row = $db->table('tb_processos')
            ->select('pk_id_processo')
            ->where([
                'nm_numero_processo' => $numero,
                'nm_ano_processo'    => $ano,
            ])
            ->where("(is_deletado IS NULL OR is_deletado = '0000-00-00 00:00:00')", null, false)
            ->get()
            ->getRow();

        return $row ? (int) $row->pk_id_processo : null;
    }

    private function montarColunasDinamicas(int $cargo, int $edital, string $busca, array $editaisAtivos, ?int $idProcesso = null): array
    {
        $rows = $this->transparenciaRepository->buscarColunasDinamicas($cargo, $edital, $busca, $editaisAtivos);
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
