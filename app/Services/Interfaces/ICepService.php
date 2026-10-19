<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface ICepService
{
    public function consultar(string $cep): array;
}
