<?php

declare(strict_types=1);

namespace App\Repositories\Publica\Interfaces;

interface IConvocacoesRepository
{
    /**
     * Lista as convocações de um processo seletivo, já vinculadas ao documento
     * publicado e ao cargo correspondente.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listarPorProcesso(int $processoId): array;

    /**
     * Lista os convocados de uma convocação, com o nome do candidato.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listarConvocadosPorConvocacao(int $convocacaoId): array;

    /**
     * Busca a situação atual de um candidato em um processo/cargo.
     */
    public function buscarSituacao(int $processoId, int $cargoId, int $candidatoId): ?array;

    /**
     * Busca a classificação de um candidato em um processo/cargo.
     */
    public function buscarClassificacao(int $processoId, int $cargoId, int $candidatoId): ?array;
}
