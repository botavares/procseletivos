<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Enums\Admin\TiposDocumentosEnum;
use CodeIgniter\Model;

class ProcessosConvocacaoModel extends Model
{
    protected $table            = 'tb_processos_convocacao';
    protected $primaryKey       = 'pk_id_convocacao';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useTimestamps    = false;
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'is_deletado';

    protected $allowedFields = [
        'fk_id_processo', //chave estrangeira referente ao processo seletivo =-)
        'fk_id_documento', //chave estrangeira referente ao documento
        'nm_numero_convocacao', //numero da convocação
        'nm_ano_convocacao', //ano da convocação
        'fk_id_cargo', //chave estrangeira referente ao cargo
    ];

    protected $validationRules = [
        'fk_id_processo'            => 'required|integer',
        'fk_id_documento'           => 'required|integer',
        'nm_numero_convocacao'      => 'required|integer',
        'nm_ano_convocacao'         => 'required|integer',
        'fk_id_cargo'               => 'required|integer',
    ];

    protected $validationMessages = [
        'fk_id_processo' => [
            'required' => 'O campo fk_id_processo é obrigatório.',
            'integer'  => 'O campo fk_id_processo deve ser um número inteiro.',
        ],
        'fk_id_documento' => [
            'required' => 'O campo fk_id_documento é obrigatório.',
            'integer'  => 'O campo fk_id_documento deve ser um número inteiro.',
        ],
        'nm_numero_convocacao' => [
            'required' => 'O campo nm_numero_convocacao é obrigatório.',
            'integer'  => 'O campo nm_numero_convocacao deve ser um número inteiro.',
        ],
        'nm_ano_convocacao' => [
            'required' => 'O campo nm_ano_convocacao é obrigatório.',
            'integer'  => 'O campo nm_ano_convocacao deve ser um número inteiro.',
        ],
        'fk_id_cargo' => [
            'required' => 'O campo fk_id_cargo é obrigatório.',
            'integer'  => 'O campo fk_id_cargo deve ser um número inteiro.',
        ]
    ];
    /**
     * Conta quantas convocações (não deletadas) existem para o processo informado.
     */
    public function contarPorProcesso(int $processoId): int
    {
        return $this->where('fk_id_processo', $processoId)
                    ->where('is_deletado IS NULL')
                    ->countAllResults();
    }

    public function listarConvocacaoProcesso(int $processoId, int $convocacaoId = null): array
    {
        $builder = $this->db->table($this->table)
            ->select('tb_processos_convocacao.*, tb_cargos.ds_nome_cargo')
            ->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_processos_convocacao.fk_id_cargo')
            ->where('tb_processos_convocacao.is_deletado', null);

        if ($processoId > 0) {
            $builder->where('tb_processos_convocacao.fk_id_processo', $processoId);
        }

        if ($convocacaoId !== null) {
            $builder->where('tb_processos_convocacao.pk_id_convocacao', $convocacaoId);
        }

        return $builder->orderBy('tb_processos_convocacao.nm_ano_convocacao', 'DESC')
                       ->orderBy('tb_processos_convocacao.nm_numero_convocacao', 'DESC')
                       ->get()
                       ->getResultArray();
    }
}