<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\DadosContratosModel;
use App\Repositories\Interfaces\IContratoRepository;

/**
 * Implementação de Repository para o domínio Contrato.
 */
class ContratoRepository implements IContratoRepository
{
    public function __construct(
        private DadosContratosModel $model
    ) {}

    public function listarTodos(): array
    {
        return $this->model->getDadosContrato();
    }

    public function buscarPorId(int $id): ?object
    {
        return $this->model->getDadoContrato($id);
    }

    public function buscarDetalhadoPorId(int $id): ?object
    {
        return $this->model->getContratosPorId($id);
    }

    public function buscarExpirando(int $prazoDias): array
    {
        return $this->model->getContratosExpirando($prazoDias);
    }

    public function buscarAtivos(): array
    {
        return $this->model->getContratosAtivos();
    }

    public function buscarPorEditalCurso(int $idEdital, int $idCurso): array
    {
        return $this->model->getContratosPorEditalCurso($idEdital, $idCurso);
    }

    public function buscarPorCandidato(int $idCandidato): array
    {
        return $this->model->getContratosPorCandidato($idCandidato);
    }

    public function buscarPorCandidatoCurso(int $idCandidato, int $idCurso): array
    {
        return $this->model->getContratosPorCandidatoCurso($idCandidato, $idCurso);
    }

    public function inserir(array $dados): bool
    {
        return $this->model->insert($dados);
    }

    public function atualizar(int $id, array $dados): bool
    {
        return $this->model->update($id, $dados);
    }

    public function deletar(int $id): bool
    {
        return $this->model->delete($id);
    }

    public function linhasAfetadas(): int
    {
        return (int) $this->model->affectedRows();
    }

    public function obterUltimoId(): int
    {
        return (int) $this->model->getInsertID();
    }
}
