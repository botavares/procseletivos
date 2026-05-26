<?php
namespace App\DTOs\Domain\Candidatos;
class SituacaoDTO {
    public ?int $id = null;
    public ?int $fk_id_candidato = null;
    public ?int $fk_id_cargo = null;
    public ?int $fk_id_edital = null;
    public ?string $situacao = null;

    public static function fromArray(array $data): SituacaoDTO {
        $dto = new SituacaoDTO();
        $dto->id = $data['pk_id_situacao'] ?? null;
        $dto->fk_id_candidato = $data['fk_id_candidato'] ?? null;
        $dto->fk_id_cargo = $data['fk_id_cargo'] ?? null;
        $dto->fk_id_edital = $data['fk_id_edital'] ?? null;
        $dto->situacao = $data['situacao'] ?? null;
        return $dto;
    }
    public function toDatabaseArray(): array {
        return [
            'pk_id_situacao' => $this->id,
            'fk_id_candidato' => $this->fk_id_candidato,
            'fk_id_cargo' => $this->fk_id_cargo,
            'fk_id_edital' => $this->fk_id_edital,
            'situacao' => $this->situacao
        ];
    }
}