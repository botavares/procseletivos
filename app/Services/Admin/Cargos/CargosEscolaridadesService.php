<?php

namespace App\Services\Admin\Cargos;

use App\Services\Admin\Base\TransactionalTrait;

use App\Models\Admin\CargosEscolaridadesModel;

class CargosEscolaridadesService 
{

    use TransactionalTrait;

    public function __construct()
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    public function salvar(array $data, string $acao = 'create'): int
    {
        return $this->transactional(function () use ($data, $acao) {
            $model = new CargosEscolaridadesModel();
            $pk = $data['pk_id_cargos_escolaridade'] ?? null;

            if ($acao === 'update' && $pk !== null) {
                if (! $model->update($pk, $data)) {
                    throw new \RuntimeException('Erro ao atualizar associação de escolaridade ao cargo.');
                }
                return (int) $pk;
            }

            $existente = $model
                ->where('fk_id_cargo', $data['fk_id_cargo'])
                ->where('fk_id_escolaridade', $data['fk_id_escolaridade'])
                ->first();

            if ($existente) {
                $id = is_array($existente) ? $existente['pk_id_cargos_escolaridade'] : $existente->pk_id_cargos_escolaridade;
                if (! $model->update($id, $data)) {
                    throw new \RuntimeException('Já existe uma associação para cargo e escolaridade.');
                }
                return (int) $id;
            }

            if (empty($data['pk_id_cargos_escolaridade'])) {
                $max = $model->selectMax('pk_id_cargos_escolaridade')->first();
                $nextId = is_array($max) ? ($max['pk_id_cargos_escolaridade'] ?? 0) + 1 : ($max->pk_id_cargos_escolaridade ?? 0) + 1;
                $data['pk_id_cargos_escolaridade'] = $nextId;
            }
            $model->insert($data);
            $insertId = $data['pk_id_cargos_escolaridade'];

            if (empty($insertId)) {
                throw new \RuntimeException('Erro ao salvar associação de escolaridade ao cargo.');
            }

            return (int) $insertId;
        });
    }

    public function deletar(int $id): void
    {
        $this->transactional(function () use ($id) {
            $model = new CargosEscolaridadesModel();
            if (! $model->delete($id)) {
                throw new \RuntimeException('Erro ao excluir a associação de escolaridade ao cargo.');
            }
        });
    }
}
