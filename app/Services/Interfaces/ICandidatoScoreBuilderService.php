<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface ICandidatoScoreBuilderService
{
    public function carregarDadosBatch(array $idsCandidatos, int $edital, int $cargo, array $configDesempate): void;

    public function construirScores(int $candidato, array $configDesempate): array;
}
