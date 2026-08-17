<?php

namespace App\Services\Cargos;

use App\Services\Base\AbstractCrudService;
use App\Models\CargosModel;
use App\DTOs\Domain\Cargos\CargoDTO;

class CargoService extends AbstractCrudService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function salvar(CargoDTO $dto): int
    {
        return $this->transactional(function () use ($dto) {
            $model = new CargosModel();
            $data = $dto->toArray();

            if (! $model->save($data)) {
                throw new \RuntimeException(
                    'Erro ao salvar Cargo: ' . implode('; ', $model->errors())
                );
            }

            $id = $dto->pk_id_cargo ?? $model->getInsertID();

            if (empty($id)) {
                throw new \RuntimeException('ID do Cargo não foi gerado');
            }

            return (int) $id;
        });
    }

    public function atualizar(CargoDTO $dto): void
    {
        $this->transactional(function () use ($dto) {
            $id = $dto->pk_id_cargo;

            if (!$id) {
                throw new \RuntimeException('ID do cargo não informado');
            }

            $model = new CargosModel();

            if (! $model->update($id, $dto->toArray())) {
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
