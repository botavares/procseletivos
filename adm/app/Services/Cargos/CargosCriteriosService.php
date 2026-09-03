<?php

namespace App\Services\Cargos;

use App\Services\Base\AbstractCrudService;
use App\Models\CargosCriteriosAdicionaisModel;
use App\DTOs\Domain\Cargos\CargosCriteriosDTO;

class CargosCriteriosService extends AbstractCrudService
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Salva (insert ou update) a associação de experiência ao cargo.
     * Se pk_id_cargos_experiencia estiver presente, faz update.
     * Caso contrário, verifica se já existe associação para o mesmo cargo + experiência
     * e atualiza; se não existir, insere um novo registro.
     */
    public function salvar(CargosCriteriosDTO $dto): int{
        return $this->transactional(function () use ($dto) {
            $model = new CargosCriteriosAdicionaisModel();

            // Se já temos o ID, é update direto
            if ($dto->pk_id_cargo_criterio !== null) {
                if (! $model->update($dto->pk_id_cargo_criterio, $dto->toArray())) {
                    throw new \RuntimeException('Erro ao atualizar associação de critério ao cargo.');
                }
                return $dto->pk_id_cargo_criterio;
            }

            // Verifica se já existe associação para o mesmo cargo + criterio
            $existente = $model
                ->where('fk_id_cargo', $dto->fk_id_cargo)
                ->where('fk_id_criterio', $dto->fk_id_criterio)
                ->first();

            if ($existente) {
                $id = is_array($existente) ? $existente['pk_id_cargo_criterio'] : $existente->pk_id_cargo_criterio;
                if (! $model->update($id, $dto->toArray())) {
                    throw new \RuntimeException('Já existe uma associação para cargo e critério.');
                }
                return (int) $id;
            }

            // Insere novo registro (tabela sem auto-increment)
            $insertData = $dto->toArray();
            // garante que a chave primária será incluída no insert
            if (!isset($insertData['pk_id_cargo_criterio']) || $insertData['pk_id_cargo_criterio'] === null) {
                // Busca o maior pk_id_cargo_criterio existente e incrementa
                $max = $model->selectMax('pk_id_cargo_criterio')->first();
                $nextId = is_array($max) ? ($max['pk_id_cargo_criterio'] ?? 0) + 1 : ($max->pk_id_cargo_criterio ?? 0) + 1;
                $insertData['pk_id_cargo_criterio'] = $nextId;
            }
            $model->insert($insertData);
            $insertId = $insertData['pk_id_cargo_criterio'];

            if (empty($insertId)) {
                throw new \RuntimeException('Erro ao salvar associação de critério ao cargo.');
            }

            return (int) $insertId;
        });
    }

    /**
     * Exclui uma associação de critério ao cargo pelo ID.
     */
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
