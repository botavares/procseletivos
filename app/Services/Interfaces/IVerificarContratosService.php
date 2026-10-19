<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IVerificarContratosService
{
    public function verificarContrato($codigoVerificacao);
}
