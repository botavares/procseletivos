<?php

namespace App\Services\Escolaridades;

use App\Services\Base\AbstractCrudService;
use App\Models\EscolaridadesModel;
use App\DTOs\Domain\Escolaridades\EscolaridadeDTO;

class EscolaridadeService extends AbstractCrudService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function salvar(EscolaridadeDTO $dto): int
    {
        return $this->transactional(function () use ($dto) {
            $model = new EscolaridadesModel();
            $data = $dto->toArray();

            if (! $model->save($data)) {
                throw new \RuntimeException(
                    'Erro ao salvar Escolaridade: ' . implode('; ', $model->errors())
                );
            }

            $id = $dto->pk_id_escolaridade ?? $model->getInsertID();

            if (empty($id)) {
                throw new \RuntimeException('ID da Escolaridade não foi gerado');
            }

            return (int) $id;
        });
    }

    public function atualizar(EscolaridadeDTO $dto): void
    {
        $this->transactional(function () use ($dto) {
            $id = $dto->pk_id_escolaridade;

            if (!$id) {
                throw new \RuntimeException('ID da escolaridade não informado');
            }

            $model = new EscolaridadesModel();

            if (! $model->update($id, $dto->toArray())) {
                throw new \RuntimeException('Erro ao atualizar escolaridade');
            }

            return true;
        });
    }

    public function deletar(int $id): void
    {
        $this->transactional(function () use ($id) {
            $model = new EscolaridadesModel();
            if (! $model->delete($id)) {
                throw new \RuntimeException('Erro ao excluir a Escolaridade');
            }
        });
    }

    public function listarPorId(int $id): ?object
    {
        $model = new EscolaridadesModel();
        return $model->find($id);
    }
}
