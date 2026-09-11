<?php

namespace App\Services\Transparencia;

use App\Models\CargosModel;
use App\Models\EditaisModel;
use App\Services\Classificacao\DesempateConfigService;
use CodeIgniter\Database\BaseConnection;

class TransparenciaService
{
    private DesempateConfigService $desempateConfigService;

    public function __construct(
        private BaseConnection $db,
        private CargosModel $cargosModel,
        private EditaisModel $editaisModel
    ) {
        $this->desempateConfigService = new DesempateConfigService();
    }

    public function listarClassificacao(array $params): array
    {
        $pagina    = max(1, (int) ($params['page'] ?? 1));
        $porPagina = max(1, (int) ($params['per_page'] ?? 10));
        $cargo     = (int) ($params['cargo'] ?? 0);
        $edital    = (int) ($params['edital'] ?? 0);
        $busca     = trim((string) ($params['search'] ?? ''));

        // Se nenhum edital for informado, filtra automaticamente pelos ativos
        $editaisAtivos = [];
        if ($edital <= 0) {
            $editaisAtivos = array_column(
                $this->db->table('tb_editais')->where('ds_status', 1)->select('pk_id_edital')->get()->getResultArray(),
                'pk_id_edital'
            );
        }

        // Verifica se o cargo tem desempate dinamico
        $usaDesempateDinamico = false;
        $colunasDinamicas = [];
        if ($cargo > 0) {
            $usaDesempateDinamico = $this->desempateConfigService->cargoPossuiConfiguracao($cargo);
            if ($usaDesempateDinamico) {
                $colunasDinamicas = $this->buscarColunasDinamicas($cargo, $edital, $busca, $editaisAtivos);
            }
        }

        // Verifica quais colunas de pontuacao estao vazias para o filtro atual (regra fixa)
        $colunasOcultas = [];
        if (!$usaDesempateDinamico) {
            $colunasOcultas = $this->obterColunasOcultas($cargo, $edital, $busca, $editaisAtivos);
        }

        $builder = $this->db->table('tb_classificacao c');
        $builder->select('c.ds_posicao, c.ds_nome_candidato, c.ds_nome_cargo, c.fk_id_edital, c.ds_nome_edital, c.dt_nascimento, c.nr_total_pontos, c.nr_total_experiencias, c.nr_total_graduacao, c.nr_total_posgraduacao, c.nr_total_mestrado, c.nr_total_doutorado, c.nr_total_aperfeicoamentos, c.fk_id_candidato, c.ds_possui_pne, sc.situacao');
        $builder->join('tb_situacao_candidato sc', 'sc.fk_id_candidato = c.fk_id_candidato AND sc.fk_id_cargo = c.fk_id_cargo AND sc.fk_id_edital = c.fk_id_edital', 'left');

        // Adiciona LEFT JOINs para scores dinamicos
        if ($usaDesempateDinamico && !empty($colunasDinamicas)) {
            foreach ($colunasDinamicas as $index => $coluna) {
                $alias = 's_' . $index;
                $builder->select("{$alias}.nr_valor as {$coluna['alias']}");
                $builder->join(
                    "tb_classificacao_scores {$alias}",
                    "{$alias}.fk_id_classificacao = c.pk_id_classificacao AND {$alias}.ds_chave_score = '{$coluna['chave']}'",
                    'left'
                );
            }
        }

        if ($cargo > 0) {
            $builder->where('c.fk_id_cargo', $cargo);
        }

        if ($edital > 0) {
            $builder->where('c.fk_id_edital', $edital);
        } elseif (!empty($editaisAtivos)) {
            $builder->whereIn('c.fk_id_edital', $editaisAtivos);
        }

        if ($busca !== '') {
            $builder->like('c.ds_nome_candidato', $busca);
        }

        $total = $builder->countAllResults(false);

        $dados = $builder
            ->orderBy('c.ds_posicao', 'ASC')
            ->limit($porPagina, ($pagina - 1) * $porPagina)
            ->get()
            ->getResult();

        // Processa a situacao de cada candidato
        foreach ($dados as $candidato) {
            $situacaoFormatada = $this->formatarSituacao($candidato->situacao ?? null);
            // Adiciona prefixo PCD se o candidato possuir deficiencia (ds_possui_pne = 1)
            if (isset($candidato->ds_possui_pne) && $candidato->ds_possui_pne == 1) {
                $candidato->ds_situacao = 'PCD - ' . $situacaoFormatada;
            } else {
                $candidato->ds_situacao = $situacaoFormatada;
            }
        }

        return [
            'dados' => $dados,
            'paginacao' => $this->calcularPaginacao($pagina, $porPagina, $total),
            'filtros' => $params,
            'total' => $total,
            'colunas_ocultas' => $colunasOcultas,
            'usa_desempate_dinamico' => $usaDesempateDinamico,
            'colunas_dinamicas' => $colunasDinamicas,
        ];
    }

    /**
     * Busca as colunas dinamicas (scores) que existem para o cargo/edital.
     * Resolve labels legíveis para chaves técnicas.
     */
    private function buscarColunasDinamicas(int $cargo, int $edital, string $busca, array $editaisAtivos = []): array
    {
        $builder = $this->db->table('tb_classificacao_scores s')
            ->select('s.ds_chave_score, s.ds_label, MIN(s.pk_id_score) as ordem')
            ->join('tb_classificacao c', 'c.pk_id_classificacao = s.fk_id_classificacao')
            ->where('c.fk_id_cargo', $cargo);

        if ($edital > 0) {
            $builder->where('c.fk_id_edital', $edital);
        } elseif (!empty($editaisAtivos)) {
            $builder->whereIn('c.fk_id_edital', $editaisAtivos);
        }

        if ($busca !== '') {
            $builder->like('c.ds_nome_candidato', $busca);
        }

        $rows = $builder
            ->groupBy('s.ds_chave_score')
            ->orderBy('ordem', 'ASC')
            ->get()
            ->getResult();

        // Carrega configuração de desempate para resolução de labels
        $configDesempate = $this->desempateConfigService->buscarConfiguracao($cargo);
        $labelsConfig = [];
        foreach ($configDesempate as $cfg) {
            $labelsConfig[$cfg->chaveScore()] = $cfg->descricao;
        }

        // Carrega nomes das referências do banco
        $nomesReferencia = $this->carregarNomesReferencias($cargo);

        $colunas = [];
        foreach ($rows as $row) {
            $chave = $row->ds_chave_score;
            $label = $row->ds_label ?? null;

            // Se o label salvo é a própria chave técnica, tenta resolver
            if (!$label || $label === $chave || str_starts_with($label, 'EXPERIENCIA_') || str_starts_with($label, 'ESCOLARIDADE_') || str_starts_with($label, 'CRITERIO_') || str_starts_with($label, 'APERFEICOAMENTO_') || str_starts_with($label, 'PONTUACAO_')) {
                // 1. Tenta label da configuração de desempate
                if (isset($labelsConfig[$chave])) {
                    $label = $labelsConfig[$chave];
                } else {
                    // 2. Tenta resolver por tipo + ID
                    $label = $this->resolverLabelPorChave($chave, $nomesReferencia);
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

    /**
     * Carrega nomes das referências (experiências, escolaridades, critérios, cursos) do cargo.
     */
    private function carregarNomesReferencias(int $cargo): array
    {
        $nomes = [];

        // Experiências
        $experiencias = $this->db->table('tb_cargos_experiencias ce')
            ->select('ce.fk_id_experiencia, e.ds_nome_experiencia')
            ->join('tb_experiencias e', 'e.pk_id_experiencia = ce.fk_id_experiencia')
            ->where('ce.fk_id_cargo', $cargo)
            ->get()
            ->getResultArray();
        foreach ($experiencias as $row) {
            $nomes['experiencias'][(int) $row['fk_id_experiencia']] = $row['ds_nome_experiencia'];
        }

        // Escolaridades
        $escolaridades = $this->db->table('tb_cargos_escolaridades ce')
            ->select('ce.fk_id_escolaridade, e.ds_nome_escolaridade')
            ->join('tb_escolaridades e', 'e.pk_id_escolaridade = ce.fk_id_escolaridade')
            ->where('ce.fk_id_cargo', $cargo)
            ->get()
            ->getResultArray();
        foreach ($escolaridades as $row) {
            $nomes['escolaridades'][(int) $row['fk_id_escolaridade']] = $row['ds_nome_escolaridade'];
        }

        // Critérios adicionais
        $criterios = $this->db->table('tb_cargos_criterios_adicionais cc')
            ->select('cc.fk_id_criterio, c.ds_nome_criterio')
            ->join('tb_criterios_adicionais c', 'c.pk_id_criterio = cc.fk_id_criterio')
            ->where('cc.fk_id_cargo', $cargo)
            ->get()
            ->getResultArray();
        foreach ($criterios as $row) {
            $nomes['criterios'][(int) $row['fk_id_criterio']] = $row['ds_nome_criterio'];
        }

        // Cursos de aperfeiçoamento
        $cursos = $this->db->table('tb_cargos_aperfeicoamentos ca')
            ->select('ca.fk_id_curso, c.ds_nome_curso')
            ->join('tb_cursos_aperfeicoamentos c', 'c.pk_id_curso = ca.fk_id_curso')
            ->where('ca.fk_id_cargo', $cargo)
            ->get()
            ->getResultArray();
        foreach ($cursos as $row) {
            $nomes['cursos'][(int) $row['fk_id_curso']] = $row['ds_nome_curso'];
        }

        return $nomes;
    }

    /**
     * Resolve um label legível a partir da chave técnica.
     */
    private function resolverLabelPorChave(string $chave, array $nomesReferencia): string
    {
        // EXPERIENCIA_ESPECIFICA_{id}
        if (str_starts_with($chave, 'EXPERIENCIA_ESPECIFICA_')) {
            $id = (int) substr($chave, strlen('EXPERIENCIA_ESPECIFICA_'));
            return $nomesReferencia['experiencias'][$id] ?? "Experiência #{$id}";
        }

        // ESCOLARIDADE_ESPECIFICA_{id}
        if (str_starts_with($chave, 'ESCOLARIDADE_ESPECIFICA_')) {
            $id = (int) substr($chave, strlen('ESCOLARIDADE_ESPECIFICA_'));
            return $nomesReferencia['escolaridades'][$id] ?? "Escolaridade #{$id}";
        }

        // CRITERIO_ADICIONAL_{id}
        if (str_starts_with($chave, 'CRITERIO_ADICIONAL_')) {
            $id = (int) substr($chave, strlen('CRITERIO_ADICIONAL_'));
            return $nomesReferencia['criterios'][$id] ?? "Critério #{$id}";
        }

        // APERFEICOAMENTO_ESPECIFICO_{id}
        if (str_starts_with($chave, 'APERFEICOAMENTO_ESPECIFICO_')) {
            $id = (int) substr($chave, strlen('APERFEICOAMENTO_ESPECIFICO_'));
            return $nomesReferencia['cursos'][$id] ?? "Aperfeiçoamento #{$id}";
        }

        // Mapeamento de chaves gerais
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

    /**
     * Verifica quais colunas de pontuacao possuem todos os valores zerados
     * ou nulos para o conjunto de registros filtrado.
     */
    private function obterColunasOcultas(int $cargo, int $edital, string $busca, array $editaisAtivos = []): array
    {
        $builder = $this->db->table('tb_classificacao c');

        if ($cargo > 0) {
            $builder->where('c.fk_id_cargo', $cargo);
        }

        if ($edital > 0) {
            $builder->where('c.fk_id_edital', $edital);
        } elseif (!empty($editaisAtivos)) {
            $builder->whereIn('c.fk_id_edital', $editaisAtivos);
        }

        if ($busca !== '') {
            $builder->like('c.ds_nome_candidato', $busca);
        }

        $row = $builder->select('
            MAX(c.nr_total_experiencias) as max_experiencias,
            MAX(c.nr_total_doutorado) as max_doutorado,
            MAX(c.nr_total_mestrado) as max_mestrado,
            MAX(c.nr_total_posgraduacao) as max_posgraduacao,
            MAX(c.nr_total_graduacao) as max_graduacao,
            MAX(c.nr_total_aperfeicoamentos) as max_aperfeicoamentos
        ')->get()->getRow();

        $ocultas = [];
        if (!$row) {
            return $ocultas;
        }

        if (empty($row->max_experiencias) || (float)$row->max_experiencias == 0) {
            $ocultas[] = 'experiencias';
        }
        if (empty($row->max_doutorado) || (float)$row->max_doutorado == 0) {
            $ocultas[] = 'doutorado';
        }
        if (empty($row->max_mestrado) || (float)$row->max_mestrado == 0) {
            $ocultas[] = 'mestrado';
        }
        if (empty($row->max_posgraduacao) || (float)$row->max_posgraduacao == 0) {
            $ocultas[] = 'posgraduacao';
        }
        if (empty($row->max_graduacao) || (float)$row->max_graduacao == 0) {
            $ocultas[] = 'graduacao';
        }
        if (empty($row->max_aperfeicoamentos) || (float)$row->max_aperfeicoamentos == 0) {
            $ocultas[] = 'aperfeicoamentos';
        }

        return $ocultas;
    }

    /**
     * Formata o codigo da situacao para texto legivel.
     * 1 = Convocado | 2 = Contratado | 3 = Eliminado | null/0 = Aguardando
     */
    private function formatarSituacao(?int $situacao): string
    {
        return match ($situacao) {
            1 => 'Convocado',
            2 => 'Contratado',
            3 => 'Eliminado',
            default => 'Aguardando',
        };
    }

    private function calcularPaginacao(
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
}
