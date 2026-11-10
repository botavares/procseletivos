<?php

declare(strict_types=1);

namespace App\Repositories\Admin\Interfaces;

/**
 * Contrato para operações de persistência de Classificações.
 */
interface IClassificacaoRepository
{
    /**
     * Lista classificação do processo/cargo com scores dinâmicos.
     */
    public function listarPorProcessoCargo(int $idProcesso, int $idCargo, ?int $idSecretaria = null): array;

    /**
     * @deprecated Mantido para compatibilidade.
     */
    public function listarPorEditalCargo(int $idEdital, int $idCargo, ?int $idSecretaria = null): array;
}
