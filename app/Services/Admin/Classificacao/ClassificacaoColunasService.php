<?php

namespace App\Services\Admin\Classificacao;

use Config\Database;

/**
 * Responsável por gerar a estrutura de colunas dinâmicas da classificação
 * baseada nas configurações de cada cargo (tb_cargos_experiencias, tb_cargos_escolaridades,
 * tb_cargos_aperfeicoamentos, tb_cargos_criterios_adicionais).
 */
class ClassificacaoColunasService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Obtém a estrutura de colunas dinâmicas baseada nas configurações do cargo.
     *
     * @param int $cargo
     * @param int|null $processo Filtra pelas configurações do processo seletivo (NULL = geral).
     * @return array Array de colunas com: chave, label, tipo, id_referencia
     */
    public function obterColunas(int $cargo, ?int $processo = null, ?int $secretaria = null): array
    {
        $colunas = [];

        // Experiências do cargo
        $builder = $this->db->table('tb_cargos_experiencias ce')
            ->select('ce.fk_id_experiencia, e.ds_nome_experiencia')
            ->join('tb_experiencias e', 'e.pk_id_experiencia = ce.fk_id_experiencia')
            ->where('ce.fk_id_cargo', $cargo)
            ->orderBy('e.ds_nome_experiencia', 'ASC');
        $this->aplicarFiltroProcesso($builder, $processo, 'ce');
        $this->aplicarFiltroSecretaria($builder, $secretaria, 'ce');
        $experiencias = $builder->get()->getResultArray();

        foreach ($experiencias as $exp) {
            $colunas[] = [
                'chave'         => 'exp_' . $exp['fk_id_experiencia'],
                'label'         => 'Pt. ' . $exp['ds_nome_experiencia'],
                'tipo'          => 'experiencia',
                'id_referencia' => (int)$exp['fk_id_experiencia'],
            ];
        }

        // Escolaridades do cargo
        $builder = $this->db->table('tb_cargos_escolaridades ce')
            ->select('ce.fk_id_escolaridade, e.ds_nome_escolaridade')
            ->join('tb_escolaridades e', 'e.pk_id_escolaridade = ce.fk_id_escolaridade')
            ->where('ce.fk_id_cargo', $cargo)
            ->orderBy('e.ds_nome_escolaridade', 'ASC');
        $this->aplicarFiltroProcesso($builder, $processo, 'ce');
        $this->aplicarFiltroSecretaria($builder, $secretaria, 'ce');
        $escolaridades = $builder->get()->getResultArray();

        foreach ($escolaridades as $esc) {
            $colunas[] = [
                'chave'         => 'esc_' . $esc['fk_id_escolaridade'],
                'label'         => 'Pt. ' . $esc['ds_nome_escolaridade'],
                'tipo'          => 'escolaridade',
                'id_referencia' => (int)$esc['fk_id_escolaridade'],
            ];
        }

        // Aperfeiçoamentos do cargo
        $builder = $this->db->table('tb_cargos_aperfeicoamentos ca')
            ->select('ca.fk_id_curso, c.ds_nome_curso')
            ->join('tb_cursos_aperfeicoamentos c', 'c.pk_id_curso = ca.fk_id_curso')
            ->where('ca.fk_id_cargo', $cargo)
            ->orderBy('c.ds_nome_curso', 'ASC');
        $this->aplicarFiltroProcesso($builder, $processo, 'ca', 'fk_id_processos_seletivo');
        $this->aplicarFiltroSecretaria($builder, $secretaria, 'ca');
        $aperfeicoamentos = $builder->get()->getResultArray();

        foreach ($aperfeicoamentos as $ap) {
            $colunas[] = [
                'chave'         => 'ape_' . $ap['fk_id_curso'],
                'label'         => 'Pt. ' . $ap['ds_nome_curso'],
                'tipo'          => 'aperfeicoamento',
                'id_referencia' => (int)$ap['fk_id_curso'],
            ];
        }

        // Critérios adicionais do cargo
        $builder = $this->db->table('tb_cargos_criterios_adicionais cc')
            ->select('cc.fk_id_criterio, c.ds_nome_criterio')
            ->join('tb_criterios_adicionais c', 'c.pk_id_criterio = cc.fk_id_criterio')
            ->where('cc.fk_id_cargo', $cargo)
            ->orderBy('c.ds_nome_criterio', 'ASC');
        $this->aplicarFiltroProcesso($builder, $processo, 'cc');
        $this->aplicarFiltroSecretaria($builder, $secretaria, 'cc');
        $criterios = $builder->get()->getResultArray();

        foreach ($criterios as $crit) {
            $colunas[] = [
                'chave'         => 'crit_' . $crit['fk_id_criterio'],
                'label'         => 'Pt. ' . $crit['ds_nome_criterio'],
                'tipo'          => 'criterio',
                'id_referencia' => (int)$crit['fk_id_criterio'],
            ];
        }

        // Colunas fixas no final da tabela
        $colunas[] = ['chave' => 'dt_nascimento', 'label' => 'Nascimento', 'tipo' => 'fixo', 'id_referencia' => null];
        $colunas[] = ['chave' => 'nr_total_pontos', 'label' => 'Total de Pontos', 'tipo' => 'fixo', 'id_referencia' => null];
        $colunas[] = ['chave' => 'ds_possui_pne', 'label' => 'PCD', 'tipo' => 'fixo', 'id_referencia' => null];

        return $colunas;
    }

    /**
     * Aplica filtro por processo seletivo em uma query de configuração de cargo.
     */
    private function aplicarFiltroProcesso($builder, ?int $processo, string $alias, string $coluna = 'fk_id_processo_seletivo'): void
    {
        if ($processo === null) {
            $builder->where("{$alias}.{$coluna} IS NULL", null, false);
        } else {
            $builder->where("{$alias}.{$coluna}", $processo);
        }
    }

    /**
     * Aplica filtro por secretaria em uma query de configuração de cargo.
     */
    private function aplicarFiltroSecretaria($builder, ?int $secretaria, string $alias, string $coluna = 'fk_id_secretaria'): void
    {
        if ($secretaria !== null) {
            $builder->where("{$alias}.{$coluna}", $secretaria);
        }
    }

    /**
     * Carrega os valores dinâmicos dos candidatos em batch.
     *
     * @param int $processo
     * @param int $cargo
     * @param array $idsCandidatos
     * @return array [candidatoId => [chave => valor]]
     */
    public function obterDadosDinamicos(int $processo, int $cargo, array $idsCandidatos, ?int $secretaria = null): array
    {
        if (empty($idsCandidatos)) {
            return [];
        }

        $dados = [];
        foreach ($idsCandidatos as $id) {
            $dados[$id] = [];
        }

        // Carrega tetos do cargo para cada tipo
        $tetosExperiencias     = $this->carregarTetos('tb_cargos_experiencias', 'fk_id_experiencia', $cargo, $secretaria);
        $tetosEscolaridades    = $this->carregarTetos('tb_cargos_escolaridades', 'fk_id_escolaridade', $cargo, $secretaria);
        $tetosAperfeicoamentos = $this->carregarTetos('tb_cargos_aperfeicoamentos', 'fk_id_curso', $cargo, $secretaria);
        $tetosCriterios        = $this->carregarTetos('tb_cargos_criterios_adicionais', 'fk_id_criterio', $cargo, $secretaria);

        // Experiências dos candidatos
        $builderExp = $this->db->table('tb_cadastrados_experiencias')
            ->select('fk_id_cadastrado, fk_id_experiencia, ds_quantidade, ds_multiplicador')
            ->where('fk_id_processo', $processo)
            ->where('fk_id_cargo', $cargo)
            ->whereIn('fk_id_cadastrado', $idsCandidatos);

        if ($secretaria !== null) {
            $builderExp->where('fk_id_secretaria', $secretaria);
        }

        $rows = $builderExp->get()->getResultArray();

        foreach ($rows as $row) {
            $idCand = (int)$row['fk_id_cadastrado'];
            $idExp  = (int)$row['fk_id_experiencia'];
            $pontuacao = (float)$row['ds_quantidade'] * (float)$row['ds_multiplicador'];
            $teto = $tetosExperiencias[$idExp] ?? null;
            if ($teto !== null && $teto > 0 && $pontuacao > $teto) {
                $pontuacao = $teto;
            }
            $chave = 'exp_' . $idExp;
            $dados[$idCand][$chave] = ($dados[$idCand][$chave] ?? 0) + $pontuacao;
        }

        // Escolaridades dos candidatos
        $builderEsc = $this->db->table('tb_cadastrados_escolaridades')
            ->select('fk_id_cadastrado, fk_id_escolaridade, ds_quantidade, ds_multiplicador')
            ->where('fk_id_processo', $processo)
            ->where('fk_id_cargo', $cargo)
            ->whereIn('fk_id_cadastrado', $idsCandidatos);

        if ($secretaria !== null) {
            $builderEsc->where('fk_id_secretaria', $secretaria);
        }

        $rows = $builderEsc->get()->getResultArray();

        foreach ($rows as $row) {
            $idCand = (int)$row['fk_id_cadastrado'];
            $idEsc  = (int)$row['fk_id_escolaridade'];
            $pontuacao = (float)$row['ds_quantidade'] * (float)$row['ds_multiplicador'];
            $teto = $tetosEscolaridades[$idEsc] ?? null;
            if ($teto !== null && $teto > 0 && $pontuacao > $teto) {
                $pontuacao = $teto;
            }
            $chave = 'esc_' . $idEsc;
            $dados[$idCand][$chave] = ($dados[$idCand][$chave] ?? 0) + $pontuacao;
        }

        // Aperfeiçoamentos dos candidatos
        $builderApe = $this->db->table('tb_cadastrados_aperfeicoamentos')
            ->select('fk_id_cadastrado, fk_id_curso, ds_quantidade, ds_multiplicador')
            ->where('fk_id_processo', $processo)
            ->where('fk_id_cargo', $cargo)
            ->whereIn('fk_id_cadastrado', $idsCandidatos);

        if ($secretaria !== null) {
            $builderApe->where('fk_id_secretaria', $secretaria);
        }

        $rows = $builderApe->get()->getResultArray();

        foreach ($rows as $row) {
            $idCand  = (int)$row['fk_id_cadastrado'];
            $idCurso = (int)$row['fk_id_curso'];
            $pontuacao = (float)$row['ds_quantidade'] * (float)$row['ds_multiplicador'];
            $teto = $tetosAperfeicoamentos[$idCurso] ?? null;
            if ($teto !== null && $teto > 0 && $pontuacao > $teto) {
                $pontuacao = $teto;
            }
            $chave = 'ape_' . $idCurso;
            $dados[$idCand][$chave] = ($dados[$idCand][$chave] ?? 0) + $pontuacao;
        }

        // Critérios adicionais dos candidatos
        $builderCrit = $this->db->table('tb_cadastrados_criterios')
            ->select('fk_id_cadastrado, fk_id_criterio, ds_quantidade, ds_multiplicador')
            ->where('fk_id_processo', $processo)
            ->where('fk_id_cargo', $cargo)
            ->whereIn('fk_id_cadastrado', $idsCandidatos);

        if ($secretaria !== null) {
            $builderCrit->where('fk_id_secretaria', $secretaria);
        }

        $rows = $builderCrit->get()->getResultArray();

        foreach ($rows as $row) {
            $idCand  = (int)$row['fk_id_cadastrado'];
            $idCrit  = (int)$row['fk_id_criterio'];
            $pontuacao = (float)($row['ds_quantidade'] ?? 0) * (float)$row['ds_multiplicador'];
            $teto = $tetosCriterios[$idCrit] ?? null;
            if ($teto !== null && $teto > 0 && $pontuacao > $teto) {
                $pontuacao = $teto;
            }
            $chave = 'crit_' . $idCrit;
            $dados[$idCand][$chave] = ($dados[$idCand][$chave] ?? 0) + $pontuacao;
        }

        return $dados;
    }

    /**
     * Carrega os tetos (pontuação máxima) de uma tabela de configuração do cargo.
     */
    private function carregarTetos(string $tabela, string $campoFk, int $cargo, ?int $secretaria = null): array
    {
        $builderTetos = $this->db->table($tabela)
            ->select("{$campoFk}, ds_pontuacao_maxima")
            ->where('fk_id_cargo', $cargo)
            ->where('ds_pontuacao_maxima >', 0);

        if ($secretaria !== null) {
            $builderTetos->where('fk_id_secretaria', $secretaria);
        }

        $rows = $builderTetos->get()->getResultArray();

        $tetos = [];
        foreach ($rows as $row) {
            $tetos[(int)$row[$campoFk]] = (float)$row['ds_pontuacao_maxima'];
        }
        return $tetos;
    }
}
