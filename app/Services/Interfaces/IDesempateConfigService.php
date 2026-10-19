<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Services\Classificacao\DTO\DesempateConfigDTO;

interface IDesempateConfigService
{
    /**
     * @return DesempateConfigDTO[]
     */
    public function buscarConfiguracao(int $cargo): array;

    public function cargoPossuiConfiguracao(int $cargo): bool;
}
