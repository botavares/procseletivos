<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\CandidatoDTO;

interface ICandidatoService
{
    public function buscarPorId(int $id): ?object;

    public function buscarPorCpf(string $cpf): ?object;

    public function salvar(CandidatoDTO $dto, string $acao, ?object $request = null): array;

    public function vincularGov(int $candidatoId, int $govId): bool;

    public function verificarStatus(string $cpf): string;

    public function buscarDeficiencia(?int $idPne): ?object;

    public function listarDeficiencias(): array;
}
