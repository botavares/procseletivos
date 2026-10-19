<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IComprovanteService
{
    public function gerarComprovanteCompleto(int $idEdital, int $idCargo, int $idCandidato): object;
}
