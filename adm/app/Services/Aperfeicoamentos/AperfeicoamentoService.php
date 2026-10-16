<?php

namespace App\Services\Aperfeicoamentos;

use App\Services\Base\AbstractCrudService;
use App\Models\AperfeicoamentosModel;
use App\DTOs\Domain\Aperfeicoamentos\AperfeicoamentoDTO;

class AperfeicoamentoService extends AbstractCrudService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function salvar(AperfeicoamentoDTO $dto): int
    {
        return $this->transactional(function () use ($dto) {
            $model = new AperfeicoamentosModel();
            $data = $dto->toArray();

            if (! $model->save($data)) {
                throw new \RuntimeException(
                    'Erro ao salvar Curso de Aperfeiçoamento: ' . implode('; ', $model->errors())
                );
            }

            $id = $dto->pk_id_curso ?? $model->getInsertID();

            if (empty($id)) {
                throw new \RuntimeException('ID do Curso não foi gerado');
            }

            return (int) $id;
        });
    }

    public function atualizar(AperfeicoamentoDTO $dto): void
    {
        $this->transactional(function () use ($dto) {
            $id = $dto->pk_id_curso;

            if (!$id) {
                throw new \RuntimeException('ID do curso não informado');
            }

            $model = new AperfeicoamentosModel();

            if (! $model->update($id, $dto->toArray())) {
                throw new \RuntimeException('Erro ao atualizar curso de aperfeiçoamento');
            }

            return true;
        });
    }

    public function deletar(int $id): void
    {
        $this->transactional(function () use ($id) {
            $model = new AperfeicoamentosModel();
            if (! $model->delete($id)) {
                throw new \RuntimeException('Erro ao excluir o Curso de Aperfeiçoamento');
            }
        });
    }

    public function listarPorId(int $id): ?object
    {
        $model = new AperfeicoamentosModel();
        return $model->find($id);
    }
}
