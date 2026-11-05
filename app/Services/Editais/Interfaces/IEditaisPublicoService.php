<?php

declare(strict_types=1);

namespace App\Services\Editais\Interfaces;

interface IEditaisPublicoService
{
    /**
     * Retorna a lista de editais (processos seletivos) formatada para o portal público,
     * já aplicando os filtros de situação, secretaria e cargo.
     *
     * @param array<string, string> $filtros
     * @return array<int, array<string, mixed>>
     */
    public function listarEditais(array $filtros = []): array;

    /**
     * Retorna as secretarias disponíveis para o filtro da página pública.
     *
     * @return array<int, object>
     */
    public function obterSecretariasFiltro(): array;

    /**
     * Retorna os cargos disponíveis para o filtro da página pública.
     *
     * @return array<int, object>
     */
    public function obterCargosFiltro(): array;
}
