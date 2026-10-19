<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IEditaisService
{
    public function listarTodosEditais(): array;

    public function buscarEditaisAtivosCargos(): array;

    public function listarEditaisAtivos(): array;

    public function listarEditaisEncerrados(): array;

    public function listarTodosCargos(): array;

    public function listarCargosPorEditais(array $editais): array;

    public function listarCargosPorEdital(int $editalId): array;

    public function listarCargosEditaisAtivos(): array;

    public function autenticarEditalPorToken(string $token): ?object;

    public function estaAtivo($idEdital);
}
