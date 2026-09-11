<?php

namespace App\Services\Classificacao;

use App\Services\Classificacao\DTO\DesempateConfigDTO;
use App\Services\Classificacao\DTO\ResultadoClassificacaoDTO;

class OrdenacaoDinamicaService
{
    /**
     * Ordena os resultados conforme configuracao de desempate.
     *
     * @param ResultadoClassificacaoDTO[] $resultados
     * @param DesempateConfigDTO[] $config
     * @return ResultadoClassificacaoDTO[]
     */
    public function ordenar(array $resultados, array $config): array
    {
        usort($resultados, function (ResultadoClassificacaoDTO $a, ResultadoClassificacaoDTO $b) use ($config) {
            $resultado = 0;

            foreach ($config as $criterio) {
                $chaveScore = $criterio->chaveScore();
                $valA = $a->scores[$chaveScore] ?? 0;
                $valB = $b->scores[$chaveScore] ?? 0;

                if ($criterio->direcao === 'DESC') {
                    $resultado = $valB <=> $valA;
                } else {
                    $resultado = $valA <=> $valB;
                }

                if ($resultado !== 0) {
                    return $resultado;
                }
            }

            return 0;
        });

        return $this->atribuirPosicoes($resultados);
    }

    /**
     * Atribui posicoes aos candidatos ordenados.
     *
     * @param ResultadoClassificacaoDTO[] $resultados
     * @return ResultadoClassificacaoDTO[]
     */
    private function atribuirPosicoes(array $resultados): array
    {
        $posicao = 1;
        foreach ($resultados as $index => $dto) {
            $resultados[$index]->ds_posicao = $posicao++;
        }
        return $resultados;
    }
}
