<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface IEditalRepository
{
    /**
     * Busca editais ativos vinculados a um cargo.
     * @return object|null
     */
    public function buscarEditaisAtivosPorCargo(int $idCargo): ?object;

    /**
     * Busca editais ativos com cargos e requisitos.
     * @return array
     */
    public function buscarEditaisAtivosComCargos(): array;

    /**
     * Busca editais por ID do candidato.
     * @return array
     */
    public function buscarEditaisPorCandidato(int $idCandidato): array;

    /**
     * Verifica se existe vínculo candidato-edital.
     * @return object|null
     */
    public function verificarVinculoCandidatoEdital(int $idCandidato, int $idEdital): ?object;
}
