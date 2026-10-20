<?php

namespace App\Services\Cargos;

use App\Services\Base\TransactionalTrait;

use App\Models\CargosModel;

class CargoService 
{
    public function __construct()
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    public function salvar(array $data): int
    {
        return $this->transactional(function () use ($data) {
            $model = new CargosModel();

            if (! $model->save($data)) {
                throw new \RuntimeException(
                    'Erro ao salvar Cargo: ' . implode('; ', $model->errors())
                );
            }

            $id = $data['pk_id_cargo'] ?? $model->getInsertID();

            if (empty($id)) {
                throw new \RuntimeException('ID do Cargo não foi gerado');
            }

            return (int) $id;
        });
    }

    public function atualizar(int $id, array $data): void
    {
        $this->transactional(function () use ($id, $data) {
            $model = new CargosModel();

            if (! $model->update($id, $data)) {
                throw new \RuntimeException('Erro ao atualizar cargo');
            }

            return true;
        });
    }

    public function deletar(int $id): void
    {
        $this->transactional(function () use ($id) {
            $model = new CargosModel();
            if (! $model->delete($id)) {
                throw new \RuntimeException('Erro ao excluir o Cargo');
            }
        });
    }

    public function listarCargoPorId(int $id): ?object
    {
        $model = new CargosModel();
        return $model->find($id);
    }

    /**
     * @deprecated Use listarCargoPorId()
     */
    public function listarCargosId($idCargo = null)
    {
        if ($idCargo !== null) {
            return $this->listarCargoPorId((int) $idCargo);
        }
        return null;
    }
}
