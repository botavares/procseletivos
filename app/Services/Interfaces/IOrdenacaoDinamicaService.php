<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Services\Classificacao\DTO\ResultadoClassificacaoDTO;

interface IOrdenacaoDinamicaService
{
    /**
     * @param ResultadoClassificacaoDTO[] $resultados
     * @param array $config
     * @return ResultadoClassificacaoDTO[]
     */
    public function ordenar(array $resultados, array $config): array;
}
