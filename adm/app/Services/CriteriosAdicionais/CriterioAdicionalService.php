<?php

namespace App\Services\CriteriosAdicionais;

use App\Services\Base\TransactionalTrait;

use App\Models\CriteriosAdicionaisModel;

class CriterioAdicionalService 
{
    public function __construct()
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    public function salvar(array $data): int
    {
        return $this->transactional(function () use ($data) {
            $model = new CriteriosAdicionaisModel();

            if (! $model->save($data)) {
                throw new \RuntimeException(
                    'Erro ao salvar Critério Adicional: ' . implode('; ', $model->errors())
                );
            }

            $id = $data['pk_id_criterio'] ?? $model->getInsertID();

            if (empty($id)) {
                throw new \RuntimeException('ID do Critério não foi gerado');
            }

            return (int) $id;
        });
    }

    public function atualizar(int $id, array $data): void
    {
        $this->transactional(function () use ($id, $data) {
            $model = new CriteriosAdicionaisModel();

            if (! $model->update($id, $data)) {
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
