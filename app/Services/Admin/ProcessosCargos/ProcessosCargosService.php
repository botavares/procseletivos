<?php

declare(strict_types=1);

namespace App\Services\Admin\ProcessosCargos;

use App\DTOs\Admin\ProcessosCargosDTO;
use App\Repositories\Admin\Interfaces\IProcessosCargosRepository;
use App\Services\Admin\Base\TransactionalTrait;

class ProcessosCargosService
{
    use TransactionalTrait;

    public function __construct(
        private IProcessosCargosRepository $repository,
        ?\Config\Database $db = null
    ) {
        $this->db = $db ?? \Config\Database::connect();
    }

    /**
     * Salva um novo vínculo processo-cargo.
     *
     * @throws \RuntimeException
     */
    public function salvar(ProcessosCargosDTO $dto): int
    {
        if ($this->repository->existeVinculo($dto->fkIdProcesso, $dto->fkIdCargo)) {
            throw new \RuntimeException('Este cargo já está vinculado a este processo.');
        }

        $id = $this->transactional(function () use ($dto) {
            if (! $this->repository->salvar($dto)) {
                $erros = $this->repository->obterErros();
                $mensagens = [];
                foreach ($erros as $campo => $msg) {
                    $mensagens[] = $campo . ': ' . $msg;
                }
                $textoErro = empty($mensagens)
                    ? 'Erro desconhecido ao salvar processo-cargo.'
                    : implode('; ', $mensagens);
                throw new \RuntimeException('Erro ao salvar Processo-Cargo: ' . $textoErro);
            }

            $id = $this->repository->obterUltimoId();
            if (empty($id)) {
                throw new \RuntimeException('ID do Processo-Cargo não foi gerado');
            }
            return $id;
        });

        return (int) $id;
    }

    /**
     * Atualiza um vínculo processo-cargo existente.
     *
     * @throws \RuntimeException
     */
    public function atualizar(int $id, ProcessosCargosDTO $dto): void
    {
        $this->transactional(function () use ($id, $dto) {
            if ($this->repository->existeVinculo($dto->fkIdProcesso, $dto->fkIdCargo, $id)) {
                throw new \RuntimeException('Este cargo já está vinculado a este processo.');
            }

            if (! $this->repository->atualizar($id, $dto)) {
                throw new \RuntimeException('Erro ao atualizar processo-cargo');
            }

            return true;
        });
    }

    /**
     * Remove um vínculo processo-cargo.
     *
     * @throws \RuntimeException
     */
    public function deletar(int $id): void
    {
        $this->transactional(function () use ($id) {
            if (! $this->repository->deletar($id)) {
                throw new \RuntimeException('Erro ao excluir o vínculo Processo-Cargo');
            }
        });
    }

    /**
     * Busca registro por ID.
     */
    public function buscarPorId(int $id): ?object
    {
        return $this->repository->buscarPorId($id);
    }

    /**
     * Lista todos os registros.
     */
    public function listarTodos(): array
    {
        return $this->repository->listarTodos();
    }

    /**
     * Busca cargos por processo seletivo.
     */
    public function buscarPorProcesso(int $processoId): array
    {
        return $this->repository->buscarPorProcesso($processoId);
    }

    /**
     * Busca registros por cargo.
     */
    public function buscarPorCargo(int $cargoId): array
    {
        return $this->repository->buscarPorCargo($cargoId);
    }
}
