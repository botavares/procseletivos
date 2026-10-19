<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\EditaisModel;
use App\Repositories\Interfaces\IEditalRepository;

/**
 * Implementação de Repository para o domínio Edital.
 */
class EditalRepository implements IEditalRepository
{
    public function __construct(
        private EditaisModel $model
    ) {}

    public function listarTodos(): array
    {
        return $this->model->getTodosEditais();
    }

    public function buscarPorId(int $id): ?object
    {
        return $this->model->getEdital($id);
    }

    public function listarAtivos(): array
    {
        return $this->model->getEditaisAtivos();
    }

    public function salvar(array $dados): bool
    {
        return $this->model->save($dados);
    }

    public function obterUltimoId(): int
    {
        return (int) $this->model->getInsertID();
    }

    public function atualizar(int $id, array $dados): bool
    {
        return $this->model->update($id, $dados);
    }

    public function deletar(int $id): bool
    {
        return $this->model->delete($id);
    }

    public function buscarCargosComContagem(): array
    {
        return $this->model->getCargosComContagemCandidatosPorEditalAtivo();
    }

    public function obterErros(): array
    {
        return $this->model->errors();
    }
}
