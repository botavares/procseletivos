<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\ITransparenciaRepository;
use CodeIgniter\Database\BaseConnection;

class TransparenciaRepository implements ITransparenciaRepository
{
    public function __construct(private BaseConnection $db) {}

    public function buscarEditaisAtivos(): array
    {
        return array_column(
            $this->db->table('tb_editais')->where('ds_status', 1)->select('pk_id_edital')->get()->getResultArray(),
            'pk_id_edital'
        );
    }

    public function listarClassificacao(
        int $pagina,
        int $porPagina,
        int $cargo,
        int $edital,
        string $busca,
        array $editaisAtivos,
        bool $usaDesempateDinamico,
        array $colunasDinamicas,
        array $criteriosAdicionais
    ): array {
        $builder = $this->db->table('tb_classificacao c');
        $builder->select('c.ds_posicao, c.ds_nome_candidato, c.ds_nome_cargo, c.fk_id_edital, c.ds_nome_edital, c.dt_nascimento, c.nr_total_pontos, c.nr_total_experiencias, c.nr_total_graduacao, c.nr_total_posgraduacao, c.nr_total_mestrado, c.nr_total_doutorado, c.nr_total_aperfeicoamentos, c.fk_id_candidato, c.ds_possui_pne, sc.situacao');
        $builder->join('tb_situacao_candidato sc', 'sc.fk_id_candidato = c.fk_id_candidato AND sc.fk_id_cargo = c.fk_id_cargo AND sc.fk_id_edital = c.fk_id_edital', 'left');

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

        if (!$usaDesempateDinamico && !empty($criteriosAdicionais)) {
            foreach ($criteriosAdicionais as $index => $crit) {
                $alias = 'crit_' . $index;
                $builder->select("COALESCE({$alias}.ds_quantidade, 0) * COALESCE({$alias}.ds_multiplicador, 0) as crit_{$crit['id']}");
                $builder->join(
                    "tb_cadastrados_criterios {$alias}",
                    "{$alias}.fk_id_cadastrado = c.fk_id_candidato AND {$alias}.fk_id_edital = c.fk_id_edital AND {$alias}.fk_id_cargo = c.fk_id_cargo AND {$alias}.fk_id_criterio = {$crit['id']}",
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

        return [
            'dados' => $dados,
            'total' => $total,
        ];
    }

    public function buscarColunasDinamicas(int $cargo, int $edital, string $busca, array $editaisAtivos): array
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

        return $builder
            ->groupBy('s.ds_chave_score')
            ->orderBy('ordem', 'ASC')
            ->get()
            ->getResult();
    }

    public function carregarNomesReferencias(int $cargo): array
    {
        $nomes = [];

        $experiencias = $this->db->table('tb_cargos_experiencias ce')
            ->select('ce.fk_id_experiencia, e.ds_nome_experiencia')
            ->join('tb_experiencias e', 'e.pk_id_experiencia = ce.fk_id_experiencia')
            ->where('ce.fk_id_cargo', $cargo)
            ->get()
            ->getResultArray();
        foreach ($experiencias as $row) {
            $nomes['experiencias'][(int) $row['fk_id_experiencia']] = $row['ds_nome_experiencia'];
        }

        $escolaridades = $this->db->table('tb_cargos_escolaridades ce')
            ->select('ce.fk_id_escolaridade, e.ds_nome_escolaridade')
            ->join('tb_escolaridades e', 'e.pk_id_escolaridade = ce.fk_id_escolaridade')
            ->where('ce.fk_id_cargo', $cargo)
            ->get()
            ->getResultArray();
        foreach ($escolaridades as $row) {
            $nomes['escolaridades'][(int) $row['fk_id_escolaridade']] = $row['ds_nome_escolaridade'];
        }

        $criterios = $this->db->table('tb_cargos_criterios_adicionais cc')
            ->select('cc.fk_id_criterio, c.ds_nome_criterio')
            ->join('tb_criterios_adicionais c', 'c.pk_id_criterio = cc.fk_id_criterio')
            ->where('cc.fk_id_cargo', $cargo)
            ->get()
            ->getResultArray();
        foreach ($criterios as $row) {
            $nomes['criterios'][(int) $row['fk_id_criterio']] = $row['ds_nome_criterio'];
        }

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

    public function obterColunasOcultas(int $cargo, int $edital, string $busca, array $editaisAtivos): array
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

    public function obterCriteriosAdicionaisAtivos(int $cargo, int $edital, string $busca, array $editaisAtivos): array
    {
        $builder = $this->db->table('tb_cadastrados_criterios cc')
            ->select('cc.fk_id_criterio, MAX(tca.ds_nome_criterio) as ds_nome_criterio')
            ->join('tb_classificacao c', 'c.fk_id_candidato = cc.fk_id_cadastrado AND c.fk_id_edital = cc.fk_id_edital AND c.fk_id_cargo = cc.fk_id_cargo')
            ->join('tb_criterios_adicionais tca', 'tca.pk_id_criterio = cc.fk_id_criterio')
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
            ->groupBy('cc.fk_id_criterio')
            ->get()
            ->getResult();

        $criterios = [];
        foreach ($rows as $row) {
            $criterios[] = [
                'id'    => (int) $row->fk_id_criterio,
                'nome'  => $row->ds_nome_criterio,
                'alias' => 'crit_' . (int) $row->fk_id_criterio,
            ];
        }

        return $criterios;
    }
}
