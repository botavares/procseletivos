<?php

namespace App\Services\Cargos;

use App\Services\Base\AbstractCrudService;
use App\Models\CargosEscolaridadesModel;
use App\DTOs\Domain\Cargos\CargosEscolaridadesDTO;

class CargosEscolaridadesService extends AbstractCrudService
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
    public function salvar(CargosEscolaridadesDTO $dto): int{
        return $this->transactional(function () use ($dto) {
            $model = new CargosEscolaridadesModel();

            // Se já temos o ID, é update direto
            if ($dto->pk_id_cargos_escolaridade !== null) {
                if (! $model->update($dto->pk_id_cargos_escolaridade, $dto->toArray())) {
                    throw new \RuntimeException('Erro ao atualizar associação de escolaridade ao cargo.');
                }
                return $dto->pk_id_cargos_escolaridade;
            }

            // Verifica se já existe associação para o mesmo cargo + escolaridade
            $existente = $model
                ->where('fk_id_cargo', $dto->fk_id_cargo)
                ->where('fk_id_escolaridade', $dto->fk_id_escolaridade)
                ->first();

            if ($existente) {
                $id = is_array($existente) ? $existente['pk_id_cargos_escolaridade'] : $existente->pk_id_cargos_escolaridade;
                if (! $model->update($id, $dto->toArray())) {
                    throw new \RuntimeException('Já existe uma associação para cargo e escolaridade.');
                }
                return (int) $id;
            }

            // Insere novo registro (tabela sem auto-increment)
            $insertData = $dto->toArray();
            // garante que a chave primária será incluída no insert
            if (!isset($insertData['pk_id_cargos_escolaridade']) || $insertData['pk_id_cargos_escolaridade'] === null) {
                // Busca o maior pk_id_cargos_escolaridade existente e incrementa
                $max = $model->selectMax('pk_id_cargos_escolaridade')->first();
                $nextId = is_array($max) ? ($max['pk_id_cargos_escolaridade'] ?? 0) + 1 : ($max->pk_id_cargos_escolaridade ?? 0) + 1;
                $insertData['pk_id_cargos_escolaridade'] = $nextId;
            }
            $model->insert($insertData);
            $insertId = $insertData['pk_id_cargos_escolaridade'];

            if (empty($insertId)) {
                throw new \RuntimeException('Erro ao salvar associação de escolaridade ao cargo.');
            }

            return (int) $insertId;
        });
    }

    /**
     * Exclui uma associação de escolaridade ao cargo pelo ID.
     */
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
