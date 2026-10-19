<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface IRecursoRepository
{
    /**
     * Busca resumo dos recursos (protocolos distintos) de um candidato
     */
    public function buscarResumoRecursosPorCandidato(int $candidatoId): array;

    /**
     * Busca detalhes completos de um recurso pelo protocolo
     */
    public function buscarDetalhesRecurso(string $protocolo): array;

    /**
     * Busca o nome de um campo em sua tabela de referência
     */
    public function buscarNomeCampo(string $categoria, int $idCampo): string;

    /**
     * Busca o tipo de campo (CHECK / INPUT) na configuração do cargo
     */
    public function buscarTipoCampo(string $categoria, int $idCargo, int $idCampo): string;
}
