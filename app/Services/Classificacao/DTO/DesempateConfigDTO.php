<?php

namespace App\Services\Classificacao\DTO;

class DesempateConfigDTO
{
    public function __construct(
        public readonly int $ordem,
        public readonly string $tipoCriterio,
        public readonly ?int $idReferencia,
        public readonly string $direcao,
        public readonly ?string $parametroExtra,
        public readonly string $descricao
    ) {}

    /**
     * Monta a chave do score no array de scores.
     *
     * @return string
     */
    public function chaveScore(): string
    {
        return match ($this->tipoCriterio) {
            'PONTUACAO_TOTAL'              => 'PONTUACAO_TOTAL',
            'PONTUACAO_EXPERIENCIAS'       => 'PONTUACAO_EXPERIENCIAS',
            'PONTUACAO_ESCOLARIDADES'      => 'PONTUACAO_ESCOLARIDADES',
            'PONTUACAO_CRITERIOS_ADICIONAIS' => 'PONTUACAO_CRITERIOS_ADICIONAIS',
            'PONTUACAO_CURSOS_APERFEICOAMENTOS' => 'PONTUACAO_CURSOS_APERFEICOAMENTOS',
            'PONTUACAO_GRADUACAO'          => 'PONTUACAO_GRADUACAO',
            'PONTUACAO_POS_GRADUACAO'      => 'PONTUACAO_POS_GRADUACAO',
            'PONTUACAO_MESTRADO'           => 'PONTUACAO_MESTRADO',
            'PONTUACAO_DOUTORADO'          => 'PONTUACAO_DOUTORADO',
            'PONTUACAO_APERFEICOAMENTOS'   => 'PONTUACAO_APERFEICOAMENTOS',
            'EXPERIENCIA_ESPECIFICA'       => "EXPERIENCIA_ESPECIFICA_{$this->idReferencia}",
            'ESCOLARIDADE_ESPECIFICA'      => "ESCOLARIDADE_ESPECIFICA_{$this->idReferencia}",
            'APERFEICOAMENTO_ESPECIFICO'   => "APERFEICOAMENTO_ESPECIFICO_{$this->idReferencia}",
            'CRITERIO_ADICIONAL'           => "CRITERIO_ADICIONAL_{$this->idReferencia}",
            'IDADE'                        => 'IDADE',
            'PNE'                          => 'PNE',
            default                        => throw new \InvalidArgumentException("Tipo de criterio desconhecido: {$this->tipoCriterio}"),
        };
    }
}
