<?php

namespace App\Services\Admin\Cargos;

use App\Services\Admin\Base\TransactionalTrait;

use App\Models\Admin\CargosModel;

class CargoService 
{

    use TransactionalTrait;

    public function __construct()
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    public function salvar(array $data): int
    {
        $model = new CargosModel();

        if (! $model->save($data)) {
            $errors = $model->errors();
            $messages = [];
            foreach ($errors as $field => $msg) {
                $messages[] = $field . ': ' . $msg;
            }
            $errorText = empty($messages) 
                ? 'Erro desconhecido ao salvar cargo.' 
                : implode('; ', $messages);
            throw new \RuntimeException('Erro ao salvar Cargo: ' . $errorText);
        }

        $id = $data['pk_id_cargo'] ?? $model->getInsertID();

        if (empty($id)) {
            throw new \RuntimeException('ID do Cargo não foi gerado');
        }

        return (int) $id;
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
