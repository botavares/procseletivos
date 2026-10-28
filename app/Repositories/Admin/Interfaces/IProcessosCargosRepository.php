<?php

declare(strict_types=1);

namespace App\Repositories\Admin\Interfaces;

use App\DTOs\Admin\ProcessosCargosDTO;

interface IProcessosCargosRepository
{
    /**
     * Lista todos os registros.
     */
    public function listarTodos(): array;

    /**
     * Busca registro por ID.
     */
    public function buscarPorId(int $id): ?object;

    /**
     * Busca cargos por processo seletivo.
     */
    public function buscarPorProcesso(int $processoId): array;

    /**
     * Busca registros por cargo.
     */
    public function buscarPorCargo(int $cargoId): array;

    /**
     * Salva um novo registro.
     */
    public function salvar(ProcessosCargosDTO $dto): bool;

    /**
     * Obtém o último ID inserido.
     */
    public function obterUltimoId(): int;

    /**
     * Atualiza um registro existente.
     */
    public function atualizar(int $id, ProcessosCargosDTO $dto): bool;

    /**
     * Remove um registro.
     */
    public function deletar(int $id): bool;

    /**
     * Verifica se já existe vínculo entre processo e cargo.
     */
    public function existeVinculo(int $processoId, int $cargoId, ?int $excetoId = null): bool;

    /**
     * Retorna erros do Model após uma operação.
     */
    public function obterErros(): array;
}
