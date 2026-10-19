<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\ProtocoloDTO;

interface IProtocoloService
{
    public function buscarOuGerar(int $candidatoId, int $cargoId, int $editalId): ProtocoloDTO;

    public function salvar(ProtocoloDTO $dto): bool;

    public function buscarPorCandidato(int $candidatoId): array;
}
