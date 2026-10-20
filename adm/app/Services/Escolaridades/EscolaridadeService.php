<?php

namespace App\Services\Escolaridades;

use App\Services\Base\TransactionalTrait;

use App\Models\EscolaridadesModel;

class EscolaridadeService 
{
    public function __construct()
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    public function salvar(array $data): int
    {
        return $this->transactional(function () use ($data) {
            $model = new EscolaridadesModel();

            if (! $model->save($data)) {
                throw new \RuntimeException(
                    'Erro ao salvar Escolaridade: ' . implode('; ', $model->errors())
                );
            }

            $id = $data['pk_id_escolaridade'] ?? $model->getInsertID();

            if (empty($id)) {
                throw new \RuntimeException('ID da Escolaridade não foi gerado');
            }

            return (int) $id;
        });
    }

    public function atualizar(int $id, array $data): void
    {
        $this->transactional(function () use ($id, $data) {
            $model = new EscolaridadesModel();

            if (! $model->update($id, $data)) {
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
