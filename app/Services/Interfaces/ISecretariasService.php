<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\SecretariasModel;

interface ISecretariasService{
    public function getSecretarias(): array;
    public function getSecretariaById(int $id): ?object;
}
