<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface ITransparenciaService
{
    public function listarClassificacao(array $params): array;
}
