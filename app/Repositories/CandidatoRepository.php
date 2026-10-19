<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\ICandidatoRepository;
use App\Models\CandidatosModel;

class CandidatoRepository implements ICandidatoRepository
{
    protected CandidatosModel $model;

    public function __construct(CandidatosModel $model)
    {
        $this->model = $model;
    }

    public function buscarPorCpf(string $cpf): ?object
    {
        return $this->model
            ->select('tb_cadastrados.*, tb_dados_academicos.*')
            ->join('tb_dados_academicos', 'tb_cadastrados.pk_id_cadastrado = tb_dados_academicos.pk_id_cadastrado')
            ->where('tb_cadastrados.ds_cpf', $cpf)
            ->orderBy('tb_cadastrados.ds_cpf', 'asc')
            ->first();
    }

    public function buscarPorId(int $id): ?object
    {
        return $this->model
            ->select('tb_cadastrados.*, tb_dados_academicos.*')
            ->join('tb_dados_academicos', 'tb_cadastrados.pk_id_cadastrado = tb_dados_academicos.pk_id_cadastrado')
            ->where('tb_cadastrados.pk_id_cadastrado', $id)
            ->orderBy('tb_cadastrados.ds_nome', 'asc')
            ->first();
    }

    public function contarCadastradosNoAno(int $ano): int
    {
        $dataInicio = "{$ano}-01-01";
        $dataFim    = "{$ano}-12-31";

        $resultado = $this->model
            ->select('COUNT(*) AS total')
            ->where('ds_data_cadastro >=', $dataInicio)
            ->where('ds_data_cadastro <=', $dataFim)
            ->first();

        return (int) ($resultado->total ?? 0);
    }
}
