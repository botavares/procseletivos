<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IEditaisService
{
    public function listarEditaisAtivos(): array;

    public function listarEditaisVigentes(): array;

    public function listarTodosEditais(): array;

    public function buscarEditaisAtivosCargos(): array;

    public function listarEditaisEncerrados(): array;

    public function listarTodosCargos(): array;

    public function listarCargosPorEditais(array $editais): array;

    public function listarCargosPorEdital(int $editalId): array;

    public function buscarPorId(int $idEdital): ?object;

    public function listarCargosEditaisAtivos(): array;

    public function autenticarEditalPorToken(string $token): ?object;

    public function estaAtivo(int $idEdital): bool;
}
