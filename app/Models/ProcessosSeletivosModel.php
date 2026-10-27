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
    public function listarTodos(): array
    {
        return $this->orderBy('pk_id_processo', 'DESC')
                    ->findAll();
    }

    /**
     * Busca cargos vinculados a um processo seletivo.
     */
    public function cargosPorProcesso(int $processoId): array
    {
        return $this->db->table('tb_processos_cargos')
            ->select('tb_cargos.pk_id_cargo, tb_cargos.ds_nome_cargo, tb_processos_cargos.nm_vagas, tb_processos_cargos.nm_vagas_pcd,tb_processos_cargos.nm_remuneracao,
            tb_processos_cargos.nm_carga_horaria,tb_processos_cargos.nm_tipo_carga_horaria,tb_processos_cargos.ds_requisito_provimento')
            ->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_processos_cargos.fk_id_cargo')
            ->where('tb_processos_cargos.fk_id_processo', $processoId)
            ->orderBy('tb_cargos.ds_nome_cargo', 'ASC')
            ->get()
            ->getResult();
    }
}
