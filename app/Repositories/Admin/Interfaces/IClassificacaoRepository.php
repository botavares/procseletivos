<?php

declare(strict_types=1);

namespace App\Repositories\Admin\Interfaces;

/**
 * Contrato para operações de persistência de Classificações.
 */
interface IClassificacaoRepository
{
    /**
     * Lista classificação do edital/cargo com scores dinâmicos.
     */
    public function listarPorEditalCargo(int $idEdital, int $idCargo): array;
}
