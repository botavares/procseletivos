<?php

namespace App\Services\Cargos;

use App\Services\Base\TransactionalTrait;

use App\Models\CargosCursosModel;

class CargosCursosService 
{
    public function __construct()
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    public function salvar(array $data, string $acao = 'create'): int
    {
        return $this->transactional(function () use ($data, $acao) {
            $model = new CargosCursosModel();
            $pk = $data['pk_id_cargo_aperfeicoamento'] ?? null;

            if ($acao === 'update' && $pk !== null) {
                if (! $model->update($pk, $data)) {
                    throw new \RuntimeException('Erro ao atualizar associação de curso ao cargo.');
                }
                return (int) $pk;
            }

            $existente = $model
                ->where('fk_id_cargo', $data['fk_id_cargo'])
                ->where('fk_id_curso', $data['fk_id_curso'])
                ->first();

            if ($existente) {
                $id = is_array($existente) ? $existente['pk_id_cargo_aperfeicoamento'] : $existente->pk_id_cargo_aperfeicoamento;
                if (! $model->update($id, $data)) {
                    throw new \RuntimeException('Já existe uma associação para cargo e curso.');
                }
                return (int) $id;
            }

            if (empty($data['pk_id_cargo_aperfeicoamento'])) {
                $max = $model->selectMax('pk_id_cargo_aperfeicoamento')->first();
                $nextId = is_array($max) ? ($max['pk_id_cargo_aperfeicoamento'] ?? 0) + 1 : ($max->pk_id_cargo_aperfeicoamento ?? 0) + 1;
                $data['pk_id_cargo_aperfeicoamento'] = $nextId;
            }
            $model->insert($data);
            $insertId = $data['pk_id_cargo_aperfeicoamento'];

            if (empty($insertId)) {
                throw new \RuntimeException('Erro ao salvar associação de curso ao cargo.');
            }

            return (int) $insertId;
        });
    }

    public function deletar(int $id): void
    {
        $this->transactional(function () use ($id) {
            $model = new CargosCursosModel();
            if (! $model->delete($id)) {
                throw new \RuntimeException('Erro ao excluir a associação de curso ao cargo.');
            }
        });
    }
}
