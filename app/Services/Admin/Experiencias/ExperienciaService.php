<?php

namespace App\Services\Admin\Experiencias;

use App\Services\Admin\Base\TransactionalTrait;

use App\Models\Admin\ExperienciasModel;

class ExperienciaService 
{

    use TransactionalTrait;

    public function __construct()
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    public function salvar(array $data): int
    {
        return $this->transactional(function () use ($data) {
            $model = new ExperienciasModel();

            if (! $model->save($data)) {
                throw new \RuntimeException(
                    'Erro ao salvar Experiência: ' . implode('; ', $model->errors())
                );
            }

            $id = $data['pk_id_experiencia'] ?? $model->getInsertID();

            if (empty($id)) {
                throw new \RuntimeException('ID da Experiência não foi gerado');
            }

            return (int) $id;
        });
    }

    public function atualizar(int $id, array $data): void
    {
        $this->transactional(function () use ($id, $data) {
            $model = new ExperienciasModel();

            if (! $model->update($id, $data)) {
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
