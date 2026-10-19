<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\GovBrUsuarioDTO;

interface IGovBrService
{
    public function autenticar(string $user): GovBrUsuarioDTO;

    public function prepararDadosSessao(object $usuario, string $userCode): array;
}
