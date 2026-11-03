<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Services\Classificacao\DTO\DesempateConfigDTO;

interface IDesempateConfigService
{
    /**
     * @param int|null $processo
     * @return DesempateConfigDTO[]
     */
    public function buscarConfiguracao(int $cargo, ?int $processo = null): array;

    /**
     * @param int|null $processo
     */
    public function cargoPossuiConfiguracao(int $cargo, ?int $processo = null): bool;
}
