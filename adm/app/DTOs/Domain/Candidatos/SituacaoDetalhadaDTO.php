<?php

namespace App\DTOs\Domain\Candidatos;

class SituacaoDetalhadaDTO
{
    public ?int $id = null;

    public ?int $fk_id_candidato = null;
    public ?string $nome_candidato = null;

    public ?int $fk_id_cargo = null;
    public ?string $nome_cargo = null;

    public ?int $fk_id_edital = null;
    public ?string $numero_edital = null;

    public ?string $situacao = null;

    public static function fromArray(array $data): self
    {
        $dto = new self();

        $dto->id = $data['pk_id_situacao'] ?? null;

        $dto->fk_id_candidato = $data['fk_id_candidato'] ?? null;
        $dto->nome_candidato = $data['ds_nome'] ?? null;

        $dto->fk_id_cargo = $data['fk_id_cargo'] ?? null;
        $dto->nome_cargo = $data['ds_nome_cargo'] ?? null;

        $dto->fk_id_edital = $data['fk_id_edital'] ?? null;
        $dto->numero_edital = $data['ds_numero_edital'] ?? null;

        $dto->situacao = $data['situacao'] ?? null;

        return $dto;
    }
}