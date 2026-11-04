<?php

declare(strict_types=1);

namespace App\Repositories\Admin;

use App\Models\Admin\CandidatosModel;
use App\Repositories\Admin\Interfaces\ICandidatoRepository;

/**
 * Implementação de Repository para o domínio Candidato.
 *
 * Encapsula todas as queries complexas do CandidatosModel,
 * expondo apenas métodos de domínio.
 */
class CandidatoRepository implements ICandidatoRepository
{
    public function __construct(
        private CandidatosModel $model
    ) {}

    public function buscarPorEditalCargo(int $idProcesso, int $idCargo): array
    {
        return $this->model->getCandidatosPorProcessoCargo($idProcesso, $idCargo);
    }

    public function buscarComRecursosPorEditalCargo(int $idProcesso, int $idCargo): array
    {
        return $this->model->getCandidatosComRecursosPorProcessoCargo($idProcesso, $idCargo);
    }

    public function buscarPorProcessoCargo(int $idProcesso, int $idCargo): array
    {
        return $this->model->getCandidatosPorProcessoCargo($idProcesso, $idCargo);
    }

    public function buscarComRecursosPorProcessoCargo(int $idProcesso, int $idCargo): array
    {
        return $this->model->getCandidatosComRecursosPorProcessoCargo($idProcesso, $idCargo);
    }

    public function buscarPorCpf(string $cpf): ?object
    {
        return $this->model->listarCandidatoCpf($cpf);
    }

    public function buscarPorId(int $processo, int $cargo, int $id): ?object
    {
        return $this->model->listarCandidatoId($processo, $cargo, $id);
    }

    public function buscarExperiencias(int $processo, int $cargo, int $id): array
    {
        return $this->model->listarExperienciaCandidato($processo, $cargo, $id);
    }

    public function buscarEscolaridades(int $processo, int $cargo, int $id): array
    {
        return $this->model->listarEscolaridadeCandidato($processo, $cargo, $id);
    }

    public function buscarAperfeicoamentos(int $processo, int $cargo, int $id): array
    {
        return $this->model->listarAperfeicoamentoCandidato($processo, $cargo, $id);
    }

    public function buscarCriterios(int $processo, int $cargo, int $id): array
    {
        return $this->model->listarCriteriosCandidato($processo, $cargo, $id);
    }

    public function contarPorAno(int $ano): int
    {
        return $this->model->contarCandidatos($ano);
    }
}
