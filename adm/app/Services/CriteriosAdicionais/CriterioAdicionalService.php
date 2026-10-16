<?php

namespace App\Services\CriteriosAdicionais;

use App\Services\Base\AbstractCrudService;
use App\Models\CriteriosAdicionaisModel;
use App\DTOs\Domain\CriteriosAdicionais\CriterioAdicionalDTO;

class CriterioAdicionalService extends AbstractCrudService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function salvar(CriterioAdicionalDTO $dto): int
    {
        return $this->transactional(function () use ($dto) {
            $model = new CriteriosAdicionaisModel();
            $data = $dto->toArray();

            if (! $model->save($data)) {
                throw new \RuntimeException(
                    'Erro ao salvar Critério Adicional: ' . implode('; ', $model->errors())
                );
            }

            $id = $dto->pk_id_criterio ?? $model->getInsertID();

            if (empty($id)) {
                throw new \RuntimeException('ID do Critério não foi gerado');
            }

            return (int) $id;
        });
    }

    public function atualizar(CriterioAdicionalDTO $dto): void
    {
        $this->transactional(function () use ($dto) {
            $id = $dto->pk_id_criterio;

            if (!$id) {
                throw new \RuntimeException('ID do critério não informado');
            }

            $model = new CriteriosAdicionaisModel();

            if (! $model->update($id, $dto->toArray())) {
                throw new \RuntimeException('Erro ao atualizar critério adicional');
            }

            return true;
        });
    }

    public function deletar(int $id): void
    {
        $this->transactional(function () use ($id) {
            $model = new CriteriosAdicionaisModel();
            if (! $model->delete($id)) {
                throw new \RuntimeException('Erro ao excluir o Critério Adicional');
            }
        });
    }

    public function listarPorId(int $id): ?object
    {
        $model = new CriteriosAdicionaisModel();
        return $model->find($id);
    }
}
