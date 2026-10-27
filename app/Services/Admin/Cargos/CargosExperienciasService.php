<?php

namespace App\Services\Admin\Cargos;

use App\Services\Admin\Base\TransactionalTrait;

use App\Models\Admin\CargosExperienciasModel;

class CargosExperienciasService 
{

    use TransactionalTrait;

    public function __construct()
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    /**
     * Salva (insert ou update) a associação de experiência ao cargo.
     */
    public function salvar(array $data, string $acao = 'create'): int
    {
        return $this->transactional(function () use ($data, $acao) {
            $model = new CargosExperienciasModel();
            $pk = $data['pk_id_cargos_experiencia'] ?? null;

            // Se ação é update e temos o ID, faz update direto
            if ($acao === 'update' && $pk !== null) {
                if (! $model->update($pk, $data)) {
                    throw new \RuntimeException('Erro ao atualizar associação de experiência ao cargo.');
                }
                return (int) $pk;
            }

            // Verifica se já existe associação para o mesmo cargo + experiência
            $existente = $model
                ->where('fk_id_cargo', $data['fk_id_cargo'])
                ->where('fk_id_experiencia', $data['fk_id_experiencia'])
                ->first();

            if ($existente) {
                $id = is_array($existente) ? $existente['pk_id_cargos_experiencia'] : $existente->pk_id_cargos_experiencia;
                if (! $model->update($id, $data)) {
                    throw new \RuntimeException('Já existe uma associação para cargo e experiência.');
                }
                return (int) $id;
            }

            // Insere novo registro (tabela sem auto-increment)
            if (empty($data['pk_id_cargos_experiencia'])) {
                $max = $model->selectMax('pk_id_cargos_experiencia')->first();
                $nextId = is_array($max) ? ($max['pk_id_cargos_experiencia'] ?? 0) + 1 : ($max->pk_id_cargos_experiencia ?? 0) + 1;
                $data['pk_id_cargos_experiencia'] = $nextId;
            }
            $model->insert($data);
            $insertId = $data['pk_id_cargos_experiencia'];

            if (empty($insertId)) {
                throw new \RuntimeException('Erro ao salvar associação de experiência ao cargo.');
            }

            return (int) $insertId;
        });
    }

    public function deletar(int $id): void
    {
        $this->transactional(function () use ($id) {
            $model = new CargosExperienciasModel();
            if (! $model->delete($id)) {
                throw new \RuntimeException('Erro ao excluir a associação de experiência ao cargo.');
            }
        });
    }
}
