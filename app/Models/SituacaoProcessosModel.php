<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class SituacaoProcessosModel extends Model
{
    protected $table            = 'tb_situacao_processos';
    protected $primaryKey       = 'pk_id_situacao_processo';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useTimestamps    = false;
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'fk_id_processo',
        'nm_id_situacao',
        'dt_ultima_alteracao',
    ];

    protected $validationRules = [
        'fk_id_processo' => 'required|integer',
        'nm_id_situacao' => 'required|integer',
    ];

    /**
     * Busca situação por ID do processo.
     */
    public function porProcesso(int $processoId): ?object
    {
        return $this->where('fk_id_processo', $processoId)->first();
    }
}
