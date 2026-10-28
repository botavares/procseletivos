<?php

declare(strict_types=1);

namespace App\Repositories\Admin;

use App\DTOs\Admin\ProcessosCargosDTO;
use App\Models\Admin\ProcessosCargosModel;
use App\Repositories\Admin\Interfaces\IProcessosCargosRepository;

class ProcessosCargosRepository implements IProcessosCargosRepository
{
    public function __construct(
        private ProcessosCargosModel $model
    ) {}

    public function listarTodos(): array
    {
        return $this->model->listarTodos();
    }

    public function buscarPorId(int $id): ?object
    {
        return $this->model->buscarPorId($id);
    }

    public function buscarPorProcesso(int $processoId): array
    {
        return $this->model->buscarPorProcesso($processoId);
    }

    public function buscarPorCargo(int $cargoId): array
    {
        return $this->model->buscarPorCargo($cargoId);
    }

    public function salvar(ProcessosCargosDTO $dto): bool
    {
        return $this->model->save($dto->toArray());
    }

    public function obterUltimoId(): int
    {
        return (int) $this->model->getInsertID();
    }

    public function atualizar(int $id, ProcessosCargosDTO $dto): bool
    {
        return $this->model->update($id, $dto->toArray());
    }

    public function deletar(int $id): bool
    {
        return $this->model->delete($id);
    }

    public function existeVinculo(int $processoId, int $cargoId, ?int $excetoId = null): bool
    {
        return $this->model->existeVinculo($processoId, $cargoId, $excetoId);
    }

    public function obterErros(): array
    {
        return $this->model->errors();
    }
}
