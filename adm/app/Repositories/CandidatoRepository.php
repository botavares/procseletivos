<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\CandidatosModel;
use App\Repositories\Interfaces\ICandidatoRepository;

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

    public function buscarPorEditalCargo(int $idEdital, int $idCargo): array
    {
        return $this->model->getCandidatosPorEditalCargo($idEdital, $idCargo);
    }

    public function buscarComRecursosPorEditalCargo(int $idEdital, int $idCargo): array
    {
        return $this->model->getCandidatosComRecursosPorEditalCargo($idEdital, $idCargo);
    }

    public function buscarPorCpf(string $cpf): ?object
    {
        return $this->model->listarCandidatoCpf($cpf);
    }

    public function buscarPorId(int $edital, int $cargo, int $id): ?object
    {
        return $this->model->listarCandidatoId($edital, $cargo, $id);
    }

    public function buscarExperiencias(int $edital, int $cargo, int $id): array
    {
        return $this->model->listarExperienciaCandidato($edital, $cargo, $id);
    }

    public function buscarEscolaridades(int $edital, int $cargo, int $id): array
    {
        return $this->model->listarEscolaridadeCandidato($edital, $cargo, $id);
    }

    public function buscarAperfeicoamentos(int $edital, int $cargo, int $id): array
    {
        return $this->model->listarAperfeicoamentoCandidato($edital, $cargo, $id);
    }

    public function buscarCriterios(int $edital, int $cargo, int $id): array
    {
        return $this->model->listarCriteriosCandidato($edital, $cargo, $id);
    }

    public function contarPorAno(int $ano): int
    {
        return $this->model->contarCandidatos($ano);
    }
}
