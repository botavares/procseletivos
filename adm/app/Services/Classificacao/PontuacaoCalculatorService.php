<?php

namespace App\Services\Classificacao;

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
     */
    public function calcular(int $candidato, int $edital, int $cargo): array
    {
        return [
            'experiencias'      => $this->calcularExperiencias($candidato, $edital, $cargo),
            'graduacao'         => $this->calcularGraduacao($candidato, $edital, $cargo),
            'posgraduacao'      => $this->calcularPosGraduacao($candidato, $edital, $cargo),
            'mestrado'          => $this->calcularMestrado($candidato, $edital, $cargo),
            'doutorado'         => $this->calcularDoutorado($candidato, $edital, $cargo),
            'aperfeicoamentos'  => $this->calcularAperfeicoamentos($candidato, $edital, $cargo),
            'criterios_adicionais' => $this->calcularCriteriosAdicionais($candidato, $edital, $cargo),
        ];
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
    private function calcularExperiencias(int $candidato, int $edital, int $cargo): float
    {
        // Busca todas as experiências do candidato
        $rows = $this->db->table('tb_cadastrados_experiencias')
            ->select('fk_id_experiencia, ds_quantidade, ds_multiplicador')
            ->where([
                'fk_id_cadastrado' => $candidato,
                'fk_id_edital'     => $edital,
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
        $limiteEdital = $this->obterLimiteEdital($edital, 'ds_total_experiencia');
        if ($limiteEdital > 0) {
            return min($total, $limiteEdital);
        }

        return $total;
    }

    // ===================================================================
    // Escolaridades (por nível)
    // ===================================================================
    private function calcularEscolaridadesPorNivel(int $candidato, int $edital, int $cargo, int $nivel): float
    {
        $rows = $this->db->table('tb_cadastrados_escolaridades escol')
            ->select('escol.fk_id_escolaridade, escol.ds_quantidade, escol.ds_multiplicador')
            ->join('tb_escolaridades e', 'e.pk_id_escolaridade = escol.fk_id_escolaridade')
            ->where([
                'escol.fk_id_cadastrado' => $candidato,
                'escol.fk_id_edital'     => $edital,
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

    private function calcularGraduacao(int $candidato, int $edital, int $cargo): float
    {
        $total = $this->calcularEscolaridadesPorNivel($candidato, $edital, $cargo, 3);
        $limiteEdital = $this->obterLimiteEdital($edital, 'ds_total_graduacao');
        return $limiteEdital > 0 ? min($total, $limiteEdital) : $total;
    }

    private function calcularPosGraduacao(int $candidato, int $edital, int $cargo): float
    {
        $total = $this->calcularEscolaridadesPorNivel($candidato, $edital, $cargo, 4);
        $limiteEdital = $this->obterLimiteEdital($edital, 'ds_total_pos_graduacao');
        return $limiteEdital > 0 ? min($total, $limiteEdital) : $total;
    }

    private function calcularMestrado(int $candidato, int $edital, int $cargo): float
    {
        $total = $this->calcularEscolaridadesPorNivel($candidato, $edital, $cargo, 5);
        $limiteEdital = $this->obterLimiteEdital($edital, 'ds_total_mestrado');
        return $limiteEdital > 0 ? min($total, $limiteEdital) : $total;
    }

    private function calcularDoutorado(int $candidato, int $edital, int $cargo): float
    {
        $total = $this->calcularEscolaridadesPorNivel($candidato, $edital, $cargo, 6);
        $limiteEdital = $this->obterLimiteEdital($edital, 'ds_total_doutorado');
        return $limiteEdital > 0 ? min($total, $limiteEdital) : $total;
    }

    // ===================================================================
    // Aperfeiçoamentos
    // ===================================================================
    private function calcularAperfeicoamentos(int $candidato, int $edital, int $cargo): float
    {
        $rows = $this->db->table('tb_cadastrados_aperfeicoamentos')
            ->select('fk_id_curso, ds_quantidade, ds_multiplicador')
            ->where([
                'fk_id_cadastrado' => $candidato,
                'fk_id_edital'     => $edital,
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

        $limiteEdital = $this->obterLimiteEdital($edital, 'ds_total_aperfeicoamento');
        return $limiteEdital > 0 ? min($total, $limiteEdital) : $total;
    }

    // ===================================================================
    // Critérios Adicionais (NOVO - estava faltando!)
    // ===================================================================
    private function calcularCriteriosAdicionais(int $candidato, int $edital, int $cargo): float
    {
        $rows = $this->db->table('tb_cadastrados_criterios')
            ->select('fk_id_criterio, ds_quantidade, ds_multiplicador')
            ->where([
                'fk_id_cadastrado' => $candidato,
                'fk_id_edital'     => $edital,
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
}
