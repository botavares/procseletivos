<?php

namespace App\DTO;

final class ClassificacaoDTO
{
    public function __construct(
        public readonly int $idCandidato,
        public readonly string $nome,
        public readonly ?string $dataNascimento,
        public readonly int $idCargo,
        public readonly int $idEdital,

        public readonly float $totalGeral,

        // Critérios de desempate
        public readonly float $tempoExperiencia,
        public readonly bool $possuiSuperiorEngenharia,
        public readonly int $quantidadePosLato,

        // Campo calculado final
        public readonly int $posicao = 0
    ) {}
}