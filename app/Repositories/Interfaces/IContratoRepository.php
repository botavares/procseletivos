<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface IContratoRepository
{
    /**
     * Busca contrato completo por ID do candidato (múltiplos JOINs).
     * @return object|null
     */
    public function buscarContratoCompletoPorCandidato(int $idCandidato): ?object;

    /**
     * Busca contratos que estão expirando dentro do prazo em dias.
     * @return array
     */
    public function buscarContratosExpirando(int $prazoDias): array;

    /**
     * Busca contratos ativos com joins de pessoais, setores e cursos.
     * @return array
     */
    public function buscarContratosAtivos(): array;

    /**
     * Busca candidatos sem contrato ativo para um edital/curso.
     * @return array
     */
    public function buscarCandidatosSemContratoAtivo(int $idEdital, int $idCurso): array;
}
