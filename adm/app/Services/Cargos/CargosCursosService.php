<?php

namespace App\Services\Cargos;

use App\Services\Base\AbstractCrudService;
use App\Models\CargosCursosModel;
use App\DTOs\Domain\Cargos\CargosCursosDTO;

class CargosCursosService extends AbstractCrudService
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Salva (insert ou update) a associação de curso ao cargo.
     * Se acao == 'update' e pk_id_cargo_aperfeicoamento estiver presente, faz update.
     * Caso contrário, verifica se já existe associação para o mesmo cargo + curso
     * e atualiza; se não existir, insere um novo registro.
     */
    public function salvar(CargosCursosDTO $dto): int{
        return $this->transactional(function () use ($dto) {
            $model = new CargosCursosModel();

            // Se ação é update e temos o ID, faz update direto
            if ($dto->getAcao() === 'update' && $dto->pk_id_cargo_aperfeicoamento !== null) {
                if (! $model->update($dto->pk_id_cargo_aperfeicoamento, $dto->toArray())) {
                    throw new \RuntimeException('Erro ao atualizar associação de curso ao cargo.');
                }
                return $dto->pk_id_cargo_aperfeicoamento;
            }

            // Verifica se já existe associação para o mesmo cargo + curso
            $existente = $model
                ->where('fk_id_cargo', $dto->fk_id_cargo)
                ->where('fk_id_curso', $dto->fk_id_curso)
                ->first();

            if ($existente) {
                $id = is_array($existente) ? $existente['pk_id_cargo_aperfeicoamento'] : $existente->pk_id_cargo_aperfeicoamento;
                if (! $model->update($id, $dto->toArray())) {
                    throw new \RuntimeException('Já existe uma associação para cargo e curso.');
                }
                return (int) $id;
            }

            // Insere novo registro (tabela sem auto-increment)
            $insertData = $dto->toArray();
            // garante que a chave primária será incluída no insert
            if (!isset($insertData['pk_id_cargo_aperfeicoamento']) || $insertData['pk_id_cargo_aperfeicoamento'] === null) {
                // Busca o maior pk_id_cargo_aperfeicoamento existente e incrementa
                $max = $model->selectMax('pk_id_cargo_aperfeicoamento')->first();
                $nextId = is_array($max) ? ($max['pk_id_cargo_aperfeicoamento'] ?? 0) + 1 : ($max->pk_id_cargo_aperfeicoamento ?? 0) + 1;
                $insertData['pk_id_cargo_aperfeicoamento'] = $nextId;
            }
            $model->insert($insertData);
            $insertId = $insertData['pk_id_cargo_aperfeicoamento'];

            if (empty($insertId)) {
                throw new \RuntimeException('Erro ao salvar associação de curso ao cargo.');
            }

            return (int) $insertId;
        });
    }

    /**
     * Exclui uma associação de curso ao cargo pelo ID.
     */
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
