<?php

declare(strict_types=1);

namespace App\Services\Publica\Interfaces;

interface IConvocacoesService
{
    /**
     * Retorna as convocações formatadas para exibição pública no painel de um
     * processo seletivo.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listarPorProcesso(int $processoId): array;
}
