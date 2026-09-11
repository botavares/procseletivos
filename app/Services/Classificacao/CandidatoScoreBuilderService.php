<?php

namespace App\Services\Classificacao;

use App\Services\Classificacao\DTO\DesempateConfigDTO;
use CodeIgniter\Database\BaseConnection;

class CandidatoScoreBuilderService
{
    private BaseConnection $db;

    // Dados carregados em batch
    private array $experiencias = [];
    private array $escolaridades = [];
    private array $criteriosAdicionais = [];
    private array $aperfeicoamentos = [];
    private array $dadosCandidatos = [];

    public function __construct(BaseConnection $db)
    {
        $this->db = $db;
    }

    /**
     * Carrega em memoria todos os dados necessarios dos candidatos de um edital/cargo.
     * Deve ser chamado uma unica vez antes de construir os scores.
     *
     * @param array $idsCandidatos
     * @param int $edital
     * @param int $cargo
     * @param array $configDesempate
     */
    public function carregarDadosBatch(array $idsCandidatos, int $edital, int $cargo, array $configDesempate): void
    {
        if (empty($idsCandidatos)) {
            return;
        }

        $tiposNecessarios = array_column($configDesempate, 'tipoCriterio');
        $refsExperiencias = $this->extrairIdsReferencia($configDesempate, 'EXPERIENCIA_ESPECIFICA');
        $refsEscolaridades = $this->extrairIdsReferencia($configDesempate, 'ESCOLARIDADE_ESPECIFICA');
        $refsCriterios = $this->extrairIdsReferencia($configDesempate, 'CRITERIO_ADICIONAL');
        $refsAperfeicoamentos = $this->extrairIdsReferencia($configDesempate, 'APERFEICOAMENTO_ESPECIFICO');

        // Carrega experiencias
        if (!empty($refsExperiencias) || in_array('PONTUACAO_EXPERIENCIAS', $tiposNecessarios)) {
            $this->experiencias = $this->carregarExperiencias($idsCandidatos, $edital, $cargo, $refsExperiencias);
        }

        // Carrega escolaridades
        if (!empty($refsEscolaridades) || in_array('PONTUACAO_ESCOLARIDADES', $tiposNecessarios) || in_array('PONTUACAO_GRADUACAO', $tiposNecessarios) || in_array('PONTUACAO_POS_GRADUACAO', $tiposNecessarios) || in_array('PONTUACAO_MESTRADO', $tiposNecessarios) || in_array('PONTUACAO_DOUTORADO', $tiposNecessarios)) {
            $this->escolaridades = $this->carregarEscolaridades($idsCandidatos, $edital, $cargo, $refsEscolaridades);
        }

        // Carrega criterios adicionais
        if (!empty($refsCriterios) || in_array('PONTUACAO_CRITERIOS_ADICIONAIS', $tiposNecessarios)) {
            $this->criteriosAdicionais = $this->carregarCriteriosAdicionais($idsCandidatos, $edital, $cargo, $refsCriterios);
        }

        // Carrega aperfeicoamentos
        if (!empty($refsAperfeicoamentos) || in_array('PONTUACAO_CURSOS_APERFEICOAMENTOS', $tiposNecessarios) || in_array('PONTUACAO_APERFEICOAMENTOS', $tiposNecessarios)) {
            $this->aperfeicoamentos = $this->carregarAperfeicoamentos($idsCandidatos, $edital, $cargo, $refsAperfeicoamentos);
        }

        // Carrega dados basicos dos candidatos (nascimento, PNE)
        if (in_array('IDADE', $tiposNecessarios) || in_array('PNE', $tiposNecessarios)) {
            $this->dadosCandidatos = $this->carregarDadosCandidatos($idsCandidatos);
        }
    }

    /**
     * Constroi o array de scores para um candidato especifico.
     *
     * @param int $candidato
     * @param array $configDesempate
     * @return array
     */
    public function construirScores(int $candidato, array $configDesempate): array
    {
        $scores = [];
        $tiposNecessarios = array_column($configDesempate, 'tipoCriterio');

        // Totais agregados
        if (in_array('PONTUACAO_TOTAL', $tiposNecessarios)) {
            $scores['PONTUACAO_TOTAL'] = $this->calcularTotalGeral($candidato);
        }
        if (in_array('PONTUACAO_EXPERIENCIAS', $tiposNecessarios)) {
            $scores['PONTUACAO_EXPERIENCIAS'] = $this->calcularTotalExperiencias($candidato);
        }
        if (in_array('PONTUACAO_ESCOLARIDADES', $tiposNecessarios)) {
            $scores['PONTUACAO_ESCOLARIDADES'] = $this->calcularTotalEscolaridades($candidato);
        }
        if (in_array('PONTUACAO_CRITERIOS_ADICIONAIS', $tiposNecessarios)) {
            $scores['PONTUACAO_CRITERIOS_ADICIONAIS'] = $this->calcularTotalCriteriosAdicionais($candidato);
        }
        if (in_array('PONTUACAO_CURSOS_APERFEICOAMENTOS', $tiposNecessarios) || in_array('PONTUACAO_APERFEICOAMENTOS', $tiposNecessarios)) {
            $scores['PONTUACAO_CURSOS_APERFEICOAMENTOS'] = $this->calcularTotalAperfeicoamentos($candidato);
        }

        // Especificos
        $idsExperiencias = $this->extrairIdsReferencia($configDesempate, 'EXPERIENCIA_ESPECIFICA');
        foreach ($idsExperiencias as $id) {
            $scores["EXPERIENCIA_ESPECIFICA_{$id}"] = $this->getScoreExperienciaEspecifica($candidato, $id);
        }

        $idsEscolaridades = $this->extrairIdsReferencia($configDesempate, 'ESCOLARIDADE_ESPECIFICA');
        foreach ($idsEscolaridades as $id) {
            $scores["ESCOLARIDADE_ESPECIFICA_{$id}"] = $this->getScoreEscolaridadeEspecifica($candidato, $id);
        }

        $idsCriterios = $this->extrairIdsReferencia($configDesempate, 'CRITERIO_ADICIONAL');
        foreach ($idsCriterios as $id) {
            $scores["CRITERIO_ADICIONAL_{$id}"] = $this->getScoreCriterioAdicional($candidato, $id);
        }

        $idsAperfeicoamentos = $this->extrairIdsReferencia($configDesempate, 'APERFEICOAMENTO_ESPECIFICO');
        foreach ($idsAperfeicoamentos as $id) {
            $scores["APERFEICOAMENTO_ESPECIFICO_{$id}"] = $this->getScoreAperfeicoamentoEspecifico($candidato, $id);
        }

        // IDADE e PNE
        if (in_array('IDADE', $tiposNecessarios)) {
            $scores['IDADE'] = $this->dadosCandidatos[$candidato]['data_nascimento'] ?? null;
        }
        if (in_array('PNE', $tiposNecessarios)) {
            $scores['PNE'] = $this->dadosCandidatos[$candidato]['possui_pne'] ?? 0;
        }

        return $scores;
    }

    // -----------------------------------------------------------------------
    // Metodos de carregamento em batch
    // -----------------------------------------------------------------------

    private function carregarExperiencias(array $idsCandidatos, int $edital, int $cargo, array $idsExperiencias): array
    {
        $builder = $this->db->table('tb_cadastrados_experiencias')
            ->select('fk_id_cadastrado, fk_id_experiencia, ds_quantidade, ds_multiplicador')
            ->where('fk_id_edital', $edital)
            ->where('fk_id_cargo', $cargo)
            ->whereIn('fk_id_cadastrado', $idsCandidatos);

        if (!empty($idsExperiencias)) {
            $builder->whereIn('fk_id_experiencia', $idsExperiencias);
        }

        $rows = $builder->get()->getResultArray();

        $dados = [];
        foreach ($rows as $row) {
            $idCandidato = (int) $row['fk_id_cadastrado'];
            $idExperiencia = (int) $row['fk_id_experiencia'];
            $pontuacao = (float) $row['ds_quantidade'] * (float) $row['ds_multiplicador'];

            if (!isset($dados[$idCandidato])) {
                $dados[$idCandidato] = [];
            }

            $dados[$idCandidato][$idExperiencia] = ($dados[$idCandidato][$idExperiencia] ?? 0) + $pontuacao;
        }

        return $dados;
    }

    private function carregarEscolaridades(array $idsCandidatos, int $edital, int $cargo, array $idsEscolaridades): array
    {
        $builder = $this->db->table('tb_cadastrados_escolaridades')
            ->select('fk_id_cadastrado, fk_id_escolaridade, ds_quantidade, ds_multiplicador')
            ->where('fk_id_edital', $edital)
            ->where('fk_id_cargo', $cargo)
            ->whereIn('fk_id_cadastrado', $idsCandidatos);

        if (!empty($idsEscolaridades)) {
            $builder->whereIn('fk_id_escolaridade', $idsEscolaridades);
        }

        $rows = $builder->get()->getResultArray();

        $dados = [];
        foreach ($rows as $row) {
            $idCandidato = (int) $row['fk_id_cadastrado'];
            $idEscolaridade = (int) $row['fk_id_escolaridade'];
            $pontuacao = (float) $row['ds_quantidade'] * (float) $row['ds_multiplicador'];

            if (!isset($dados[$idCandidato])) {
                $dados[$idCandidato] = [];
            }

            $dados[$idCandidato][$idEscolaridade] = ($dados[$idCandidato][$idEscolaridade] ?? 0) + $pontuacao;
        }

        return $dados;
    }

    private function carregarCriteriosAdicionais(array $idsCandidatos, int $edital, int $cargo, array $idsCriterios): array
    {
        $builder = $this->db->table('tb_cadastrados_criterios')
            ->select('fk_id_cadastrado, fk_id_criterio, ds_quantidade, ds_multiplicador')
            ->where('fk_id_edital', $edital)
            ->where('fk_id_cargo', $cargo)
            ->whereIn('fk_id_cadastrado', $idsCandidatos);

        if (!empty($idsCriterios)) {
            $builder->whereIn('fk_id_criterio', $idsCriterios);
        }

        $rows = $builder->get()->getResultArray();

        $dados = [];
        foreach ($rows as $row) {
            $idCandidato = (int) $row['fk_id_cadastrado'];
            $idCriterio = (int) $row['fk_id_criterio'];
            $pontuacao = (float) $row['ds_quantidade'] * (float) $row['ds_multiplicador'];

            if (!isset($dados[$idCandidato])) {
                $dados[$idCandidato] = [];
            }

            $dados[$idCandidato][$idCriterio] = ($dados[$idCandidato][$idCriterio] ?? 0) + $pontuacao;
        }

        return $dados;
    }

    private function carregarAperfeicoamentos(array $idsCandidatos, int $edital, int $cargo, array $idsAperfeicoamentos): array
    {
        $builder = $this->db->table('tb_cadastrados_aperfeicoamentos')
            ->select('fk_id_cadastrado, fk_id_curso, ds_quantidade, ds_multiplicador')
            ->where('fk_id_edital', $edital)
            ->where('fk_id_cargo', $cargo)
            ->whereIn('fk_id_cadastrado', $idsCandidatos);

        if (!empty($idsAperfeicoamentos)) {
            $builder->whereIn('fk_id_curso', $idsAperfeicoamentos);
        }

        $rows = $builder->get()->getResultArray();

        $dados = [];
        foreach ($rows as $row) {
            $idCandidato = (int) $row['fk_id_cadastrado'];
            $idCurso = (int) $row['fk_id_curso'];
            $pontuacao = (float) $row['ds_quantidade'] * (float) $row['ds_multiplicador'];

            if (!isset($dados[$idCandidato])) {
                $dados[$idCandidato] = [];
            }

            $dados[$idCandidato][$idCurso] = ($dados[$idCandidato][$idCurso] ?? 0) + $pontuacao;
        }

        return $dados;
    }

    private function carregarDadosCandidatos(array $idsCandidatos): array
    {
        $rows = $this->db->table('tb_cadastrados')
            ->select('pk_id_cadastrado, dt_nascimento, ds_possui_deficiencia')
            ->whereIn('pk_id_cadastrado', $idsCandidatos)
            ->get()
            ->getResultArray();

        $dados = [];
        foreach ($rows as $row) {
            $dados[(int) $row['pk_id_cadastrado']] = [
                'data_nascimento' => $row['dt_nascimento'] ?? null,
                'possui_pne'      => (int) ($row['ds_possui_deficiencia'] ?? 0),
            ];
        }

        return $dados;
    }

    // -----------------------------------------------------------------------
    // Metodos de calculo de scores
    // -----------------------------------------------------------------------

    private function calcularTotalGeral(int $candidato): float
    {
        return
            $this->calcularTotalExperiencias($candidato)
            + $this->calcularTotalEscolaridades($candidato)
            + $this->calcularTotalCriteriosAdicionais($candidato)
            + $this->calcularTotalAperfeicoamentos($candidato);
    }

    private function calcularTotalExperiencias(int $candidato): float
    {
        $total = 0;
        foreach ($this->experiencias[$candidato] ?? [] as $pontuacao) {
            $total += $pontuacao;
        }
        return $total;
    }

    private function calcularTotalEscolaridades(int $candidato): float
    {
        $total = 0;
        foreach ($this->escolaridades[$candidato] ?? [] as $pontuacao) {
            $total += $pontuacao;
        }
        return $total;
    }

    private function calcularTotalCriteriosAdicionais(int $candidato): float
    {
        $total = 0;
        foreach ($this->criteriosAdicionais[$candidato] ?? [] as $pontuacao) {
            $total += $pontuacao;
        }
        return $total;
    }

    private function calcularTotalAperfeicoamentos(int $candidato): float
    {
        $total = 0;
        foreach ($this->aperfeicoamentos[$candidato] ?? [] as $pontuacao) {
            $total += $pontuacao;
        }
        return $total;
    }

    private function getScoreExperienciaEspecifica(int $candidato, int $idExperiencia): float
    {
        return $this->experiencias[$candidato][$idExperiencia] ?? 0;
    }

    private function getScoreEscolaridadeEspecifica(int $candidato, int $idEscolaridade): float
    {
        return $this->escolaridades[$candidato][$idEscolaridade] ?? 0;
    }

    private function getScoreCriterioAdicional(int $candidato, int $idCriterio): float
    {
        return $this->criteriosAdicionais[$candidato][$idCriterio] ?? 0;
    }

    private function getScoreAperfeicoamentoEspecifico(int $candidato, int $idCurso): float
    {
        return $this->aperfeicoamentos[$candidato][$idCurso] ?? 0;
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /**
     * Extrai os IDs de referencia de um tipo especifico da configuracao.
     *
     * @param array $configDesempate
     * @param string $tipoCriterio
     * @return array
     */
    private function extrairIdsReferencia(array $configDesempate, string $tipoCriterio): array
    {
        $ids = [];
        foreach ($configDesempate as $criterio) {
            if ($criterio instanceof DesempateConfigDTO) {
                if ($criterio->tipoCriterio === $tipoCriterio && $criterio->idReferencia !== null) {
                    $ids[] = $criterio->idReferencia;
                }
            } elseif (is_array($criterio) && $criterio['tipoCriterio'] === $tipoCriterio && !empty($criterio['idReferencia'])) {
                $ids[] = (int) $criterio['idReferencia'];
            } elseif (is_object($criterio) && $criterio->tipoCriterio === $tipoCriterio && !empty($criterio->idReferencia)) {
                $ids[] = (int) $criterio->idReferencia;
            }
        }
        return array_unique($ids);
    }
}
