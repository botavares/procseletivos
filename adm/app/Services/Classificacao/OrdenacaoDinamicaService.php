<?php

namespace App\Services\Classificacao;

use App\Services\Classificacao\DTO\DesempateConfigDTO;
use App\Services\Classificacao\DTO\ResultadoClassificacaoDTO;
use DateTimeImmutable;

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
            foreach ($config as $criterio) {
                $chaveScore = $criterio->chaveScore();
                $comparacao = $this->compararCriterio($criterio, $a, $b, $chaveScore);

                if ($comparacao !== 0) {
                    return $comparacao;
                }
            }

            return 0;
        });

        return $this->atribuirPosicoes($resultados);
    }

    /**
     * Compara dois candidatos em um criterio especifico.
     */
    private function compararCriterio(DesempateConfigDTO $criterio, ResultadoClassificacaoDTO $a, ResultadoClassificacaoDTO $b, string $chaveScore): int
    {
        if ($criterio->tipoCriterio === 'IDADE') {
            return $this->compararIdade($criterio, $a, $b, $chaveScore);
        }

        if ($criterio->tipoCriterio === 'PNE') {
            return $this->compararPne($criterio, $a, $b, $chaveScore);
        }

        // Critérios numéricos (pontuações)
        $valA = (float) ($a->scores[$chaveScore] ?? 0);
        $valB = (float) ($b->scores[$chaveScore] ?? 0);

        if ($criterio->direcao === 'DESC') {
            return $valB <=> $valA;
        }
        return $valA <=> $valB;
    }

    /**
     * Compara datas de nascimento.
     * DESC = mais velho primeiro (data menor vem primeiro)
     * ASC  = mais novo primeiro (data maior vem primeiro)
     */
    private function compararIdade(DesempateConfigDTO $criterio, ResultadoClassificacaoDTO $a, ResultadoClassificacaoDTO $b, string $chaveScore): int
    {
        $dtA = $this->parseData($a->scores[$chaveScore] ?? $a->ds_nascimento);
        $dtB = $this->parseData($b->scores[$chaveScore] ?? $b->ds_nascimento);

        if ($criterio->direcao === 'DESC') {
            // Mais velho primeiro: data MENOR (nascido antes) vem primeiro
            return $dtA <=> $dtB;
        }
        // Mais novo primeiro: data MAIOR vem primeiro
        return $dtB <=> $dtA;
    }

    /**
     * Compara flag PNE.
     */
    private function compararPne(DesempateConfigDTO $criterio, ResultadoClassificacaoDTO $a, ResultadoClassificacaoDTO $b, string $chaveScore): int
    {
        $valA = (int) ($a->scores[$chaveScore] ?? 0);
        $valB = (int) ($b->scores[$chaveScore] ?? 0);

        if ($criterio->direcao === 'DESC') {
            return $valB <=> $valA;
        }
        return $valA <=> $valB;
    }

    /**
     * Parseia data de nascimento em string formatada (Y-m-d).
     */
    private function parseData(mixed $valor): string
    {
        if (empty($valor) || $valor === '0000-00-00') {
            return '9999-12-31';
        }

        $str = (string) $valor;

        // Já é Y-m-d
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $str)) {
            return $str;
        }

        // Formato d/m/Y
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $str, $m)) {
            return "{$m[3]}-{$m[2]}-{$m[1]}";
        }

        // Tenta DateTime
        try {
            $dt = new DateTimeImmutable($str);
            return $dt->format('Y-m-d');
        } catch (\Exception $e) {
            return '9999-12-31';
        }
    }

    /**
     * Atribui posicoes aos candidatos ordenados.
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
