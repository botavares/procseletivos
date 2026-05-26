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

        $builder = $this->db->table('tb_classificacao c');
        $builder->select('c.ds_posicao, c.ds_nome_candidato, c.ds_nome_cargo, c.fk_id_edital,c.ds_nome_edital, c.dt_nascimento, c.nr_total_pontos, c.nr_total_experiencias, c.nr_total_graduacao, c.nr_total_posgraduacao, c.nr_total_aperfeicoamentos, c.fk_id_candidato, c.ds_possui_pne, sc.situacao');
        $builder->join('tb_situacao_candidato sc', 'sc.fk_id_candidato = c.fk_id_candidato AND sc.fk_id_cargo = c.fk_id_cargo AND sc.fk_id_edital = c.fk_id_edital', 'left');

        if ($cargo > 0) {
            $builder->where('c.fk_id_cargo', $cargo);
        }

        if ($edital > 0) {
            $builder->where('c.fk_id_edital', $edital);
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
            'total' => $total
        ];
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