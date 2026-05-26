<?php

namespace App\Domain\Classificacao;

use App\DTO\ClassificacaoDTO;

final class ClassificacaoCalculator
{
    /**
     * Ordena conforme edital:
     * 1 - total geral
     * 2 - tempo experiência
     * 3 - superior engenharia
     * 4 - pós lato
     * 5 - idade
     */
    public function ordenar(array $candidatos): array
    {
        usort($candidatos, function (ClassificacaoDTO $a, ClassificacaoDTO $b) {

            return
                $b->totalGeral <=> $a->totalGeral
                ?: $b->tempoExperiencia <=> $a->tempoExperiencia
                ?: $b->possuiSuperiorEngenharia <=> $a->possuiSuperiorEngenharia
                ?: $b->quantidadePosLato <=> $a->quantidadePosLato
                ?: strtotime($a->dataNascimento) <=> strtotime($b->dataNascimento);
        });

        return $this->atribuirPosicoes($candidatos);
    }

    private function atribuirPosicoes(array $candidatos): array
    {
        $posicao = 1;

        foreach ($candidatos as $index => $dto) {
            $candidatos[$index] = new ClassificacaoDTO(
                ...get_object_vars($dto),
                posicao: $posicao++
            );
        }

        return $candidatos;
    }
}