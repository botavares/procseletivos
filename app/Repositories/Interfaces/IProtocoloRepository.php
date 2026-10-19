<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface IProtocoloRepository
{
    /**
     * Busca protocolos do candidato com joins de edital e cargo.
     * @return array
     */
    public function buscarProtocolosDoCandidato(int $idCandidato): array;
}
