<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IClassificatorioService
{
    public function buscarDadosCargo(int $cargoId): ?object;

    public function buscarRequisitos(int $cargoId): array;

    public function buscarDadosCadastrados(int $candidatoId, int $cargoId, int $editalId): array;

    public function processarFormulario(array $post, int $candidatoId, int $cargoId, int $editalId): bool;
}
