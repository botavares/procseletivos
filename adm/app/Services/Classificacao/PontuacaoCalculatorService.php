<?php

namespace App\Services\Classificacao;

use CodeIgniter\Database\BaseConnection;

/**
 * Responsável exclusivamente por calcular
 * a pontuação individual de um candidato.
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
            'experiencias'  => $this->calcularExperiencias($candidato, $edital, $cargo),
            'graduacao'     => $this->calcularGraduacao($candidato, $edital, $cargo),
            'posgraduacao'  => $this->calcularPosGraduacao($candidato, $edital, $cargo),
            'mestrado'      => $this->calcularMestrado($candidato, $edital, $cargo),
            'doutorado'     => $this->calcularDoutorado($candidato, $edital, $cargo),
            'aperfeicoamentos' => $this->calcularAperfeicoamentos($candidato, $edital, $cargo)
        ];
    }

    private function calcularExperiencias(int $candidato, int $edital, int $cargo): float{
        $resultado = $this->db->table('tb_cadastrados_experiencias')
            ->select('COALESCE(SUM(ds_quantidade * ds_multiplicador), 0) AS total')
            ->where([
                'fk_id_cadastrado' => $candidato,
                'fk_id_edital'     => $edital,
                'fk_id_cargo'      => $cargo,
                'ds_status'        => 0
            ])
            ->get()
            ->getRow();
        $totalCalculado = (float) ($resultado->total ?? 0);
        $limiteRow = $this->db->table('tb_pontuacoes_edital')
            ->select('ds_total_experiencia')
            ->where('fk_id_edital', $edital)
            ->get()
            ->getRow();
        $limite = (float) ($limiteRow->ds_total_experiencia ?? 0);

        // Aplica o limite se houver
        if ($limite > 0) {
            return min($totalCalculado, $limite);
        }
        return $totalCalculado;
    }

    private function calcularGraduacao(int $candidato, int $edital, int $cargo): float    {
        $resultado = $this->db->table('tb_cadastrados_escolaridades escol')
        ->select('COALESCE(SUM(escol.ds_quantidade * escol.ds_multiplicador), 0) AS total')
        ->join('tb_escolaridades e', 'e.pk_id_escolaridade = escol.fk_id_escolaridade', 'inner')
        ->where([
            'escol.fk_id_cadastrado' => $candidato,
            'escol.fk_id_edital'     => $edital,
            'escol.fk_id_cargo'      => $cargo,
            'escol.ds_status'        => 0,
            'e.fk_id_nivel'      => 3 // 3 = graduação
        ])
        ->get()
        ->getRow();
        $totalCalculado = (float) ($resultado->total ?? 0);
        $limiteRow = $this->db->table('tb_pontuacoes_edital')
        ->select('ds_total_graduacao')
        ->where('fk_id_edital', $edital)
        ->get()
        ->getRow();
        $limite = (float) ($limiteRow->ds_total_graduacao ?? 0);
        
        // Aplica o limite se houver
        if ($limite > 0) {
            return min($totalCalculado, $limite);
        }
        return $totalCalculado;
    }

    private function calcularPosGraduacao(int $candidato, int $edital, int $cargo): float
    {
        $resultado = $this->db->table('tb_cadastrados_escolaridades escol')
        ->select('COALESCE(SUM(escol.ds_quantidade * escol.ds_multiplicador), 0) AS total')
        ->join('tb_escolaridades e', 'e.pk_id_escolaridade = escol.fk_id_escolaridade')
        ->where([
            'escol.fk_id_cadastrado' => $candidato,
            'escol.fk_id_edital'     => $edital,
            'escol.fk_id_cargo'      => $cargo,
            'escol.ds_status'        => 0,
            'e.fk_id_nivel'      => 4 // 4 = posgraduação
        ])
        ->get()
        ->getRow();
        $totalCalculado = (float) ($resultado->total ?? 0);
        $limiteRow = $this->db->table('tb_pontuacoes_edital')
        ->select('ds_total_pos_graduacao')
        ->where('fk_id_edital', $edital)
        ->get()
        ->getRow();
        $limite = (float) ($limiteRow->ds_total_pos_graduacao ?? 0);

        // Aplica o limite se houver
        if ($limite > 0) {
            return min($totalCalculado, $limite);
        }
        return $totalCalculado;
    }

    private function calcularMestrado(int $candidato, int $edital, int $cargo): float
    {
        $resultado = $this->db->table('tb_cadastrados_escolaridades escol')
        ->select('COALESCE(SUM(escol.ds_quantidade * escol.ds_multiplicador), 0) AS total')
        ->join('tb_escolaridades e', 'e.pk_id_escolaridade = escol.fk_id_escolaridade')
        ->where([
            'escol.fk_id_cadastrado' => $candidato,
            'escol.fk_id_edital'     => $edital,
            'escol.fk_id_cargo'      => $cargo,
            'escol.ds_status'        => 0,
            'e.fk_id_nivel'      => 5 // 5 = mestrado
        ])
        ->get()
        ->getRow();
        $totalCalculado = (float) ($resultado->total ?? 0);
        $limiteRow = $this->db->table('tb_pontuacoes_edital')
        ->select('ds_total_mestrado')
        ->where('fk_id_edital', $edital)
        ->get()
        ->getRow();
        $limite = (float) ($limiteRow->ds_total_mestrado ?? 0);

        // Aplica o limite se houver
        if ($limite > 0) {
            return min($totalCalculado, $limite);
        }
        return $totalCalculado;
    }

        

    private function calcularDoutorado(int $candidato, int $edital, int $cargo): float
    {
        $resultado  = $this->db->table('tb_cadastrados_escolaridades escol')
        ->select('COALESCE(SUM(escol.ds_quantidade * escol.ds_multiplicador), 0) AS total')
        ->join('tb_escolaridades e', 'e.pk_id_escolaridade = escol.fk_id_escolaridade')
        ->where([
            'escol.fk_id_cadastrado' => $candidato,
            'escol.fk_id_edital'     => $edital,
            'escol.fk_id_cargo'      => $cargo,
            'escol.ds_status'        => 0,
            'e.fk_id_nivel'      => 6 // 6 = doutorado
        ])
        ->get()
        ->getRow();
        $totalCalculado = (float) ($resultado->total ?? 0);
        $limiteRow = $this->db->table('tb_pontuacoes_edital')
        ->select('ds_total_doutorado')
        ->where('fk_id_edital', $edital)
        ->get()
        ->getRow();
        $limite = (float) ($limiteRow->ds_total_doutorado ?? 0);

        // Aplica o limite se houver
        if ($limite > 0) {
            return min($totalCalculado, $limite);
        }
        return $totalCalculado;
    }
    private function calcularAperfeicoamentos(int $candidato, int $edital, int $cargo): float
    {
        $resultado = $this->db->table('tb_cadastrados_aperfeicoamentos')
            ->select('COALESCE(SUM(ds_quantidade * ds_multiplicador), 0) AS total')
            ->where([
                'fk_id_cadastrado' => $candidato,
                'fk_id_edital'     => $edital,
                'fk_id_cargo'      => $cargo,
                'ds_status'        => 0
            ])
            ->get()
            ->getRow();

        $totalCalculado = (float) ($resultado->total ?? 0);
        $limiteRow = $this->db->table('tb_pontuacoes_edital')
            ->select('ds_total_aperfeicoamento')
            ->where('fk_id_edital', $edital)
            ->get()
            ->getRow();
        $limite = (float) ($limiteRow->ds_total_aperfeicoamento ?? 0);

        // Aplica o limite se houver
        if ($limite > 0) {
            return min($totalCalculado, $limite);
        }
        return $totalCalculado;
        
    }
}