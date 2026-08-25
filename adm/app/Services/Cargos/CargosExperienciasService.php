<?php

namespace App\Services\Cargos;

use App\Services\Base\AbstractCrudService;
use App\Models\CargosExperienciasModel;
use App\DTOs\Domain\Cargos\CargosExperienciasDTO;

class CargosExperienciasService extends AbstractCrudService
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
    public function salvar(CargosExperienciasDTO $dto): int{
        return $this->transactional(function () use ($dto) {
            $model = new CargosExperienciasModel();

            // Se já temos o ID, é update direto
            if ($dto->pk_id_cargos_experiencia !== null) {
                if (! $model->update($dto->pk_id_cargos_experiencia, $dto->toArray())) {
                    throw new \RuntimeException('Erro ao atualizar associação de experiência ao cargo.');
                }
                return $dto->pk_id_cargos_experiencia;
            }

            // Verifica se já existe associação para o mesmo cargo + experiência
            $existente = $model
                ->where('fk_id_cargo', $dto->fk_id_cargo)
                ->where('fk_id_experiencia', $dto->fk_id_experiencia)
                ->first();

            if ($existente) {
                $id = is_array($existente) ? $existente['pk_id_cargos_experiencia'] : $existente->pk_id_cargos_experiencia;
                if (! $model->update($id, $dto->toArray())) {
                    throw new \RuntimeException('Já existe uma associação para cargo e experiência.');
                }
                return (int) $id;
            }

            // Insere novo registro (tabela sem auto-increment)
            $insertData = $dto->toArray();
            // garante que a chave primária será incluída no insert
            if (!isset($insertData['pk_id_cargos_experiencia']) || $insertData['pk_id_cargos_experiencia'] === null) {
                // Busca o maior pk_id_cargos_experiencia existente e incrementa
                $max = $model->selectMax('pk_id_cargos_experiencia')->first();
                $nextId = is_array($max) ? ($max['pk_id_cargos_experiencia'] ?? 0) + 1 : ($max->pk_id_cargos_experiencia ?? 0) + 1;
                $insertData['pk_id_cargos_experiencia'] = $nextId;
            }
            $model->insert($insertData);
            $insertId = $insertData['pk_id_cargos_experiencia'];

            if (empty($insertId)) {
                throw new \RuntimeException('Erro ao salvar associação de experiência ao cargo.');
            }

            return (int) $insertId;
        });
    }

    /**
     * Exclui uma associação de experiência ao cargo pelo ID.
     */
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
