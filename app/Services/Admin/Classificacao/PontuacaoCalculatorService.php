<?php

namespace App\Services\Admin\Classificacao;

use CodeIgniter\Database\BaseConnection;

/**
 * Responsável exclusivamente por calcular
 * a pontuação individual de um candidato.
 *
 * Aplica tetos (ds_pontuacao_maxima) por item de configuração do cargo.
 *
 * NÃO faz insert.
 * NÃO gera ranking.
 */
class PontuacaoCalculatorService
{
    public function __construct(
        private BaseConnection $db
    ) {}

    /**
     * Calcula todas as pontuações de um candidato.
     *
     * As tabelas classificatórias do candidato foram migradas para fk_id_processo;
     * os tetos por processo ainda são lidos via edital legado (tb_pontuacoes_edital).
     */
    public function calcular(int $candidato, int $processo, int $cargo): array
    {
        return [
            'experiencias'      => $this->calcularExperiencias($candidato, $processo, $cargo),
            'graduacao'         => $this->calcularGraduacao($candidato, $processo, $cargo),
            'posgraduacao'      => $this->calcularPosGraduacao($candidato, $processo, $cargo),
            'mestrado'          => $this->calcularMestrado($candidato, $processo, $cargo),
            'doutorado'         => $this->calcularDoutorado($candidato, $processo, $cargo),
            'aperfeicoamentos'  => $this->calcularAperfeicoamentos($candidato, $processo, $cargo),
            'criterios_adicionais' => $this->calcularCriteriosAdicionais($candidato, $processo, $cargo),
        ];
    }

    /**
     * Resolve o edital vinculado ao processo seletivo (numero/ano).
     */
    private function resolverEditalPorProcesso(int $processo): ?int
    {
        $row = $this->db
            ->table('tb_processos')
            ->select('nm_numero_processo, nm_ano_processo')
            ->where('pk_id_processo', $processo)
            ->where("(is_deletado IS NULL OR is_deletado = '0000-00-00 00:00:00')", null, false)
            ->get()
            ->getRow();

        if (!$row) {
            return null;
        }

        $numeroCompleto = ((int) $row->nm_numero_processo * 10000) + (int) $row->nm_ano_processo;

        $edital = $this->db
            ->table('tb_editais')
            ->select('pk_id_edital')
            ->where('ds_numero_edital', $numeroCompleto)
            ->get()
            ->getRow();

        return $edital ? (int) $edital->pk_id_edital : null;
    }

    /**
     * Aplica o limite individual de um item (teto).
     */
    private function aplicarTeto(float $pontuacao, ?float $teto): float
    {
        if ($teto !== null && $teto > 0 && $pontuacao > $teto) {
            return $teto;
        }
        return $pontuacao;
    }

    // ===================================================================
    // Experiências
    // ===================================================================
    private function calcularExperiencias(int $candidato, int $processo, int $cargo): float
    {
        // Busca todas as experiências do candidato
        $rows = $this->db->table('tb_cadastrados_experiencias')
            ->select('fk_id_experiencia, ds_quantidade, ds_multiplicador')
            ->where([
                'fk_id_cadastrado' => $candidato,
                'fk_id_processo'   => $processo,
                'fk_id_cargo'      => $cargo
            ])
            ->get()
            ->getResultArray();

        // Carrega tetos do cargo
        $tetos = [];
        $tetosRows = $this->db->table('tb_cargos_experiencias')
            ->select('fk_id_experiencia, ds_pontuacao_maxima')
            ->where('fk_id_cargo', $cargo)
            ->where('ds_pontuacao_maxima >', 0)
            ->get()
            ->getResultArray();
        foreach ($tetosRows as $row) {
            $tetos[(int) $row['fk_id_experiencia']] = (float) $row['ds_pontuacao_maxima'];
        }

        // Soma item por item aplicando tetos
        $total = 0;
        foreach ($rows as $row) {
            $idExp = (int) $row['fk_id_experiencia'];
            $pontuacao = (float) $row['ds_quantidade'] * (float) $row['ds_multiplicador'];
            $total += $this->aplicarTeto($pontuacao, $tetos[$idExp] ?? null);
        }

        // Aplica limite do edital se houver
        $limiteEdital = $this->obterLimiteEditalPorProcesso($processo, 'ds_total_experiencia');
        if ($limiteEdital > 0) {
            return min($total, $limiteEdital);
        }

        return $total;
    }

    // ===================================================================
    // Escolaridades (por nível)
    // ===================================================================
    private function calcularEscolaridadesPorNivel(int $candidato, int $processo, int $cargo, int $nivel): float
    {
        $rows = $this->db->table('tb_cadastrados_escolaridades escol')
            ->select('escol.fk_id_escolaridade, escol.ds_quantidade, escol.ds_multiplicador')
            ->join('tb_escolaridades e', 'e.pk_id_escolaridade = escol.fk_id_escolaridade')
            ->where([
                'escol.fk_id_cadastrado' => $candidato,
                'escol.fk_id_processo'   => $processo,
                'escol.fk_id_cargo'      => $cargo,
                'e.fk_id_nivel'          => $nivel
            ])
            ->get()
            ->getResultArray();

        // Carrega tetos do cargo
        $tetos = [];
        $tetosRows = $this->db->table('tb_cargos_escolaridades')
            ->select('fk_id_escolaridade, ds_pontuacao_maxima')
            ->where('fk_id_cargo', $cargo)
            ->where('ds_pontuacao_maxima >', 0)
            ->get()
            ->getResultArray();
        foreach ($tetosRows as $row) {
            $tetos[(int) $row['fk_id_escolaridade']] = (float) $row['ds_pontuacao_maxima'];
        }

        $total = 0;
        foreach ($rows as $row) {
            $idEsc = (int) $row['fk_id_escolaridade'];
            $pontuacao = (float) $row['ds_quantidade'] * (float) $row['ds_multiplicador'];
            $total += $this->aplicarTeto($pontuacao, $tetos[$idEsc] ?? null);
        }

        return $total;
    }

    private function calcularGraduacao(int $candidato, int $processo, int $cargo): float
    {
        $total = $this->calcularEscolaridadesPorNivel($candidato, $processo, $cargo, 3);
        $limiteEdital = $this->obterLimiteEditalPorProcesso($processo, 'ds_total_graduacao');
        return $limiteEdital > 0 ? min($total, $limiteEdital) : $total;
    }

    private function calcularPosGraduacao(int $candidato, int $processo, int $cargo): float
    {
        $total = $this->calcularEscolaridadesPorNivel($candidato, $processo, $cargo, 4);
        $limiteEdital = $this->obterLimiteEditalPorProcesso($processo, 'ds_total_pos_graduacao');
        return $limiteEdital > 0 ? min($total, $limiteEdital) : $total;
    }

    private function calcularMestrado(int $candidato, int $processo, int $cargo): float
    {
        $total = $this->calcularEscolaridadesPorNivel($candidato, $processo, $cargo, 5);
        $limiteEdital = $this->obterLimiteEditalPorProcesso($processo, 'ds_total_mestrado');
        return $limiteEdital > 0 ? min($total, $limiteEdital) : $total;
    }

    private function calcularDoutorado(int $candidato, int $processo, int $cargo): float
    {
        $total = $this->calcularEscolaridadesPorNivel($candidato, $processo, $cargo, 6);
        $limiteEdital = $this->obterLimiteEditalPorProcesso($processo, 'ds_total_doutorado');
        return $limiteEdital > 0 ? min($total, $limiteEdital) : $total;
    }

    // ===================================================================
    // Aperfeiçoamentos
    // ===================================================================
    private function calcularAperfeicoamentos(int $candidato, int $processo, int $cargo): float
    {
        $rows = $this->db->table('tb_cadastrados_aperfeicoamentos')
            ->select('fk_id_curso, ds_quantidade, ds_multiplicador')
            ->where([
                'fk_id_cadastrado' => $candidato,
                'fk_id_processo'   => $processo,
                'fk_id_cargo'      => $cargo
            ])
            ->get()
            ->getResultArray();

        // Carrega tetos do cargo
        $tetos = [];
        $tetosRows = $this->db->table('tb_cargos_aperfeicoamentos')
            ->select('fk_id_curso, ds_pontuacao_maxima')
            ->where('fk_id_cargo', $cargo)
            ->where('ds_pontuacao_maxima >', 0)
            ->get()
            ->getResultArray();
        foreach ($tetosRows as $row) {
            $tetos[(int) $row['fk_id_curso']] = (float) $row['ds_pontuacao_maxima'];
        }

        $total = 0;
        foreach ($rows as $row) {
            $idCurso = (int) $row['fk_id_curso'];
            $pontuacao = (float) $row['ds_quantidade'] * (float) $row['ds_multiplicador'];
            $total += $this->aplicarTeto($pontuacao, $tetos[$idCurso] ?? null);
        }

        $limiteEdital = $this->obterLimiteEditalPorProcesso($processo, 'ds_total_aperfeicoamento');
        return $limiteEdital > 0 ? min($total, $limiteEdital) : $total;
    }

    // ===================================================================
    // Critérios Adicionais (NOVO - estava faltando!)
    // ===================================================================
    private function calcularCriteriosAdicionais(int $candidato, int $processo, int $cargo): float
    {
        $rows = $this->db->table('tb_cadastrados_criterios')
            ->select('fk_id_criterio, ds_quantidade, ds_multiplicador')
            ->where([
                'fk_id_cadastrado' => $candidato,
                'fk_id_processo'   => $processo,
                'fk_id_cargo'      => $cargo
            ])
            ->get()
            ->getResultArray();

        // Carrega tetos do cargo
        $tetos = [];
        $tetosRows = $this->db->table('tb_cargos_criterios_adicionais')
            ->select('fk_id_criterio, ds_pontuacao_maxima')
            ->where('fk_id_cargo', $cargo)
            ->where('ds_pontuacao_maxima >', 0)
            ->get()
            ->getResultArray();
        foreach ($tetosRows as $row) {
            $tetos[(int) $row['fk_id_criterio']] = (float) $row['ds_pontuacao_maxima'];
        }

        $total = 0;
        foreach ($rows as $row) {
            $idCriterio = (int) $row['fk_id_criterio'];
            $pontuacao = (float) $row['ds_quantidade'] * (float) $row['ds_multiplicador'];
            $total += $this->aplicarTeto($pontuacao, $tetos[$idCriterio] ?? null);
        }

        return $total;
    }

    // ===================================================================
    // Helper
    // ===================================================================
    private function obterLimiteEdital(int $edital, string $campo): float
    {
        $row = $this->db->table('tb_pontuacoes_edital')
            ->select($campo)
            ->where('fk_id_edital', $edital)
            ->get()
            ->getRow();

        return (float) ($row->{$campo} ?? 0);
    }

    private function obterLimiteEditalPorProcesso(int $processo, string $campo): float
    {
        $edital = $this->resolverEditalPorProcesso($processo);
        if (!$edital) {
            return 0;
        }

        return $this->obterLimiteEdital($edital, $campo);
    }
}
