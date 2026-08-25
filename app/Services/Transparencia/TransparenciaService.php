<?php

namespace App\Services\Transparencia;

use App\Models\CargosModel;
use App\Models\EditaisModel;
use CodeIgniter\Database\BaseConnection;

class TransparenciaService
{
    public function __construct(
        private BaseConnection $db,
        private CargosModel $cargosModel,
        private EditaisModel $editaisModel
    ) {}

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

        // Verifica quais colunas de pontuação estão vazias para o filtro atual
        $colunasOcultas = $this->obterColunasOcultas($cargo, $edital, $busca, $editaisAtivos);

        $builder = $this->db->table('tb_classificacao c');
        $builder->select('c.ds_posicao, c.ds_nome_candidato, c.ds_nome_cargo, c.fk_id_edital,c.ds_nome_edital, c.dt_nascimento, c.nr_total_pontos, c.nr_total_experiencias, c.nr_total_graduacao, c.nr_total_posgraduacao, c.nr_total_mestrado, c.nr_total_doutorado, c.nr_total_aperfeicoamentos, c.fk_id_candidato, c.ds_possui_pne, sc.situacao');
        $builder->join('tb_situacao_candidato sc', 'sc.fk_id_candidato = c.fk_id_candidato AND sc.fk_id_cargo = c.fk_id_cargo AND sc.fk_id_edital = c.fk_id_edital', 'left');

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

        // Processa a situação de cada candidato
        foreach ($dados as $candidato) {
            $situacaoFormatada = $this->formatarSituacao($candidato->situacao ?? null);
            // Adiciona prefixo PCD se o candidato possuir deficiência (ds_possui_pne = 1)
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
        ];
    }

    /**
     * Verifica quais colunas de pontuação possuem todos os valores zerados
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
     * Formata o código da situação para texto legível.
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
