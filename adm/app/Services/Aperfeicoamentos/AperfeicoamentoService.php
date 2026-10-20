<?php

namespace App\Services\Aperfeicoamentos;

use App\Services\Base\TransactionalTrait;

use App\Models\AperfeicoamentosModel;

class AperfeicoamentoService 
{
    public function __construct()
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    public function salvar(array $data): int
    {
        return $this->transactional(function () use ($data) {
            $model = new AperfeicoamentosModel();

            if (! $model->save($data)) {
                throw new \RuntimeException(
                    'Erro ao salvar Curso de Aperfeiçoamento: ' . implode('; ', $model->errors())
                );
            }

            $id = $data['pk_id_curso'] ?? $model->getInsertID();

            if (empty($id)) {
                throw new \RuntimeException('ID do Curso não foi gerado');
            }

            return (int) $id;
        });
    }

    public function atualizar(int $id, array $data): void
    {
        $this->transactional(function () use ($id, $data) {
            $model = new AperfeicoamentosModel();

            if (! $model->update($id, $data)) {
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
