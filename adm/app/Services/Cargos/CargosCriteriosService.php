<?php

namespace App\Services\Cargos;

use App\Services\Base\TransactionalTrait;

use App\Models\CargosCriteriosAdicionaisModel;

class CargosCriteriosService 
{
    public function __construct()
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    public function salvar(array $data, string $acao = 'create'): int
    {
        return $this->transactional(function () use ($data, $acao) {
            $model = new CargosCriteriosAdicionaisModel();
            $pk = $data['pk_id_cargo_criterio'] ?? null;

            if ($acao === 'update' && $pk !== null) {
                if (! $model->update($pk, $data)) {
                    throw new \RuntimeException('Erro ao atualizar associação de critério ao cargo.');
                }
                return (int) $pk;
            }

            $existente = $model
                ->where('fk_id_cargo', $data['fk_id_cargo'])
                ->where('fk_id_criterio', $data['fk_id_criterio'])
                ->first();

            if ($existente) {
                $id = is_array($existente) ? $existente['pk_id_cargo_criterio'] : $existente->pk_id_cargo_criterio;
                if (! $model->update($id, $data)) {
                    throw new \RuntimeException('Já existe uma associação para cargo e critério.');
                }
                return (int) $id;
            }

            if (empty($data['pk_id_cargo_criterio'])) {
                $max = $model->selectMax('pk_id_cargo_criterio')->first();
                $nextId = is_array($max) ? ($max['pk_id_cargo_criterio'] ?? 0) + 1 : ($max->pk_id_cargo_criterio ?? 0) + 1;
                $data['pk_id_cargo_criterio'] = $nextId;
            }
            $model->insert($data);
            $insertId = $data['pk_id_cargo_criterio'];

            if (empty($insertId)) {
                throw new \RuntimeException('Erro ao salvar associação de critério ao cargo.');
            }

            return (int) $insertId;
        });
    }

    public function deletar(int $id): void
    {
        $this->transactional(function () use ($id) {
            $model = new CargosCriteriosAdicionaisModel();
            if (! $model->delete($id)) {
                throw new \RuntimeException('Erro ao excluir a associação de critério ao cargo.');
            }
        });
    }
}
