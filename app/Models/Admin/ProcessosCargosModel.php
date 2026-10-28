<?php

declare(strict_types=1);

namespace App\Models\Admin;

use CodeIgniter\Model;

class ProcessosCargosModel extends Model
{
    protected $table            = 'tb_processos_cargos';
    protected $primaryKey       = 'pk_id_processo_cargo';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useTimestamps    = false;
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'fk_id_processo',
        'fk_id_cargo',
        'nm_vagas',
        'nm_vagas_pcd',
        'nm_cadastro_reserva',
        'ds_requisito_provimento',
        'nm_carga_horaria',
        'nm_tipo_carga_horaria',
        'nm_remuneracao',
    ];

    protected $validationRules = [
        'fk_id_processo'          => 'required|integer',
        'fk_id_cargo'             => 'required|integer',
        'nm_vagas'                => 'required|integer',
        'nm_vagas_pcd'            => 'permit_empty|integer',
        'nm_cadastro_reserva'     => 'required|integer|in_list[0,1]',
        'ds_requisito_provimento' => 'required|string|max_length[255]',
        'nm_carga_horaria'        => 'required|integer',
        'nm_tipo_carga_horaria'   => 'required|integer',
        'nm_remuneracao'          => 'required|decimal',
    ];

    protected $validationMessages = [
        'fk_id_processo' => [
            'required' => 'O processo é obrigatório.',
            'integer'  => 'O ID do processo deve ser um número inteiro.',
        ],
        'fk_id_cargo' => [
            'required' => 'O cargo é obrigatório.',
            'integer'  => 'O ID do cargo deve ser um número inteiro.',
        ],
        'nm_vagas' => [
            'required' => 'O número de vagas é obrigatório.',
            'integer'  => 'O número de vagas deve ser um número inteiro.',
        ],
        'nm_cadastro_reserva' => [
            'required'  => 'O campo cadastro reserva é obrigatório.',
            'integer'   => 'O campo cadastro reserva deve ser um número inteiro.',
            'in_list'   => 'O campo cadastro reserva deve ser 0 ou 1.',
        ],
        'ds_requisito_provimento' => [
            'required'    => 'O requisito de provimento é obrigatório.',
            'max_length'  => 'O requisito de provimento deve ter no máximo 255 caracteres.',
        ],
        'nm_carga_horaria' => [
            'required' => 'A carga horária é obrigatória.',
            'integer'  => 'A carga horária deve ser um número inteiro.',
        ],
        'nm_tipo_carga_horaria' => [
            'required' => 'O tipo de carga horária é obrigatório.',
            'integer'  => 'O tipo de carga horária deve ser um número inteiro.',
        ],
        'nm_remuneracao' => [
            'required' => 'A remuneração é obrigatória.',
            'decimal'  => 'A remuneração deve ser um valor decimal válido.',
        ],
    ];

    /**
     * Retorna todos os registros ordenados pelo ID descendente.
     */
    public function listarTodos(): array
    {
        return $this->orderBy('pk_id_processo_cargo', 'DESC')
                    ->findAll();
    }

    /**
     * Busca registro por ID.
     */
    public function buscarPorId(int $id): ?object
    {
        return $this->where('pk_id_processo_cargo', $id)
                    ->first();
    }

    /**
     * Busca cargos por processo seletivo.
     */
    public function buscarPorProcesso(int $processoId): array
    {
        return $this->where('fk_id_processo', $processoId)
                    ->findAll();
    }

    /**
     * Busca registros por cargo.
     */
    public function buscarPorCargo(int $cargoId): array
    {
        return $this->where('fk_id_cargo', $cargoId)
                    ->findAll();
    }

    /**
     * Verifica se já existe um cargo vinculado ao processo.
     */
    public function existeVinculo(int $processoId, int $cargoId, ?int $excetoId = null): bool
    {
        $builder = $this->where('fk_id_processo', $processoId)
                          ->where('fk_id_cargo', $cargoId);

        if ($excetoId !== null) {
            $builder->where('pk_id_processo_cargo !=', $excetoId);
        }

        return (bool) $builder->first();
    }
}
