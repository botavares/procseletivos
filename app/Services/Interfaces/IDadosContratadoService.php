<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IDadosContratadoService
{
    public function getDadosContratado($idCandidato);

    public function contagemDeFerias($contratado, $setor);
}
