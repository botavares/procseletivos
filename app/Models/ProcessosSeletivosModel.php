<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ProcessosSeletivosModel extends Model
{
    protected $table            = 'tb_processos';
    protected $primaryKey       = 'pk_id_processo';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useTimestamps    = false;
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'is_deletado';

    protected $allowedFields = [
        'fk_id_secretaria',
        'nm_numero_processo',
        'nm_ano_processo',
        'ds_objeto',
        'nm_situacao',
        'dt_inicio_inscricao',
        'dt_fim_inscricao',
        'dt_divulga_resultado',
        'dt_data_homologacao',
        'nm_meses_validade',
        'nm_prorrogado',
        'is_deletado',
    ];

    protected $validationRules = [
        'fk_id_secretaria'   => 'required|integer',
        'nm_numero_processo' => 'required|integer',
        'nm_ano_processo'    => 'required|integer',
        'ds_objeto'          => 'required|string|max_length[255]',
        'nm_situacao'        => 'required|integer',
        'dt_inicio_inscricao'  => 'required|valid_date[Y-m-d]',
        'dt_fim_inscricao'     => 'required|valid_date[Y-m-d]',
        'nm_meses_validade'  => 'required|integer',
        'nm_prorrogado'      => 'required|integer|in_list[0,1]',
    ];

    /**
     * Consulta processos por ano.
     */
    public function porAno(int $ano): array
    {
        return $this->where('nm_ano_processo', $ano)
                    ->findAll();
    }

    /**
     * Consulta processos por secretaria.
     */
    public function porSecretaria(int $idSecretaria): array
    {
        return $this->where('fk_id_secretaria', $idSecretaria)
                    ->findAll();
    }

    /**
     * Consulta por número de processo e ano.
     */
    public function porNumeroEAno(int $numero, int $ano): ?object
    {
        return $this->where('nm_numero_processo', $numero)
                    ->where('nm_ano_processo', $ano)
                    ->first();
    }

    /**
     * Consulta processos por situação.
     */
    public function porSituacao(int $situacao): array
    {
        return $this->where('nm_situacao', $situacao)
                    ->findAll();
    }

    /**
     * Consulta processos por faixa de tempo (inclusive nas extremidades).
     */
    public function porFaixaDeTempo(string $dataInicio, string $dataFim): array
    {
        return $this->where("dt_inicio_inscricao >=", $dataInicio)
                    ->where("dt_fim_inscricao <=", $dataFim)
                    ->findAll();
    }

    /**
     * Retorna todos os processos não deletados ordenados pelo ID descendente.
     */
    public function listarTodos(?int $idSecretaria = null): array
    {
        $builder = $this->orderBy('pk_id_processo', 'DESC');

        if ($idSecretaria !== null && $idSecretaria > 0) {
            $builder = $this->where('fk_id_secretaria', $idSecretaria);
        }

        return $builder->findAll();
    }

    /**
     * Busca cargos vinculados a um processo seletivo.
     */
    public function cargosPorProcesso(int $processoId, ?int $idSecretaria = null): array
    {
        $builder = $this->db->table('tb_processos_cargos')
            ->select('tb_cargos.pk_id_cargo, tb_cargos.ds_nome_cargo, tb_cargos.fk_id_secretaria, tb_processos_cargos.nm_vagas, tb_processos_cargos.nm_vagas_pcd,tb_processos_cargos.nm_remuneracao,
            tb_processos_cargos.nm_carga_horaria,tb_processos_cargos.nm_tipo_carga_horaria,tb_processos_cargos.ds_requisito_provimento')
            ->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_processos_cargos.fk_id_cargo')
            ->where('tb_processos_cargos.fk_id_processo', $processoId);

        if ($idSecretaria !== null && $idSecretaria > 0) {
            $builder->where('tb_cargos.fk_id_secretaria', $idSecretaria);
        }

        return $builder->orderBy('tb_cargos.ds_nome_cargo', 'ASC')
            ->get()
            ->getResult();
    }

    /**
     * Lista todos os processos não deletados com situação atual e secretaria.
     * Usado pelo portal público de editais.
     */
    public function listarComSituacaoESecretaria(): array
    {
        return $this->db->table('tb_processos')
            ->select(
                'tb_processos.*,' .
                ' tb_situacao_processos.nm_id_situacao,' .
                ' tb_situacao_processos.dt_ultima_alteracao,' .
                ' tb_secretarias.ds_nome_secretaria,' .
                ' tb_secretarias.ds_sigla_secretaria'
            )
            ->join('tb_situacao_processos', 'tb_situacao_processos.fk_id_processo = tb_processos.pk_id_processo', 'left')
            ->join('tb_secretarias', 'tb_secretarias.pk_id_secretaria = tb_processos.fk_id_secretaria', 'left')
            ->where("(tb_processos.is_deletado IS NULL OR tb_processos.is_deletado = '0000-00-00 00:00:00')")
            ->groupBy('tb_processos.pk_id_processo')
            ->orderBy('tb_processos.pk_id_processo', 'DESC')
            ->get()
            ->getResult();
    }

    /**
     * Retorna os cargos dos processos seletivos com inscrições abertas ou agendadas,
     * unindo com as requisitos classificatórios existentes.
     */
    public function cargosPorProcessosAtivos(): array
    {
        return $this->db->table('tb_processos_cargos')
            ->select(
                'tb_processos_cargos.*,' .
                ' tb_cargos.ds_nome_cargo,' .
                ' tb_processos.nm_numero_processo,' .
                ' tb_processos.nm_ano_processo,' .
                ' tb_processos.pk_id_processo AS fk_id_processo,' .
                ' (tb_processos.nm_numero_processo * 10000 + tb_processos.nm_ano_processo) AS ds_numero_edital'
            )
            ->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_processos_cargos.fk_id_cargo')
            ->join('tb_processos', 'tb_processos.pk_id_processo = tb_processos_cargos.fk_id_processo')
            ->join('tb_situacao_processos', 'tb_situacao_processos.fk_id_processo = tb_processos.pk_id_processo')
            ->where("(tb_processos.is_deletado IS NULL OR tb_processos.is_deletado = '0000-00-00 00:00:00')")
            ->whereIn('tb_situacao_processos.nm_id_situacao', [1, 6]) // INSCRICOES_ABERTAS ou AGENDADO
            ->groupStart()
                ->where("EXISTS (
                    SELECT 1 FROM tb_cargos_experiencias e
                    WHERE e.fk_id_cargo = tb_processos_cargos.fk_id_cargo
                )", null, false)
                ->orWhere("EXISTS (
                    SELECT 1 FROM tb_cargos_aperfeicoamentos a
                    WHERE a.fk_id_cargo = tb_processos_cargos.fk_id_cargo
                )", null, false)
                ->orWhere("EXISTS (
                    SELECT 1 FROM tb_cargos_escolaridades s
                    WHERE s.fk_id_cargo = tb_processos_cargos.fk_id_cargo
                )", null, false)
                ->orWhere("EXISTS (
                    SELECT 1 FROM tb_cargos_criterios_adicionais c
                    WHERE c.fk_id_cargo = tb_processos_cargos.fk_id_cargo
                )", null, false)
            ->groupEnd()
            ->orderBy('tb_processos.nm_numero_processo', 'DESC')
            ->orderBy('tb_cargos.ds_nome_cargo', 'ASC')
            ->get()
            ->getResult();
    }
}
