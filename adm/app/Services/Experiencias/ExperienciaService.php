<?php

namespace App\Services\Experiencias;

use App\Services\Base\AbstractCrudService;
use App\Models\ExperienciasModel;
use App\DTOs\Domain\Experiencias\ExperienciaDTO;

class ExperienciaService extends AbstractCrudService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function salvar(ExperienciaDTO $dto): int
    {
        return $this->transactional(function () use ($dto) {
            $model = new ExperienciasModel();
            $data = $dto->toArray();

            if (! $model->save($data)) {
                throw new \RuntimeException(
                    'Erro ao salvar Experiência: ' . implode('; ', $model->errors())
                );
            }

            $id = $dto->pk_id_experiencia ?? $model->getInsertID();

            if (empty($id)) {
                throw new \RuntimeException('ID da Experiência não foi gerado');
            }

            return (int) $id;
        });
    }

    public function atualizar(ExperienciaDTO $dto): void
    {
        $this->transactional(function () use ($dto) {
            $id = $dto->pk_id_experiencia;

            if (!$id) {
                throw new \RuntimeException('ID da experiência não informado');
            }

            $model = new ExperienciasModel();

            if (! $model->update($id, $dto->toArray())) {
                throw new \RuntimeException('Erro ao atualizar experiência');
            }

            return true;
        });
    }

    public function deletar(int $id): void
    {
        $this->transactional(function () use ($id) {
            $model = new ExperienciasModel();
            if (! $model->delete($id)) {
                throw new \RuntimeException('Erro ao excluir a Experiência');
            }
        });
    }

    public function listarPorId(int $id): ?object
    {
        $model = new ExperienciasModel();
        return $model->find($id);
    }
}
