<?php

namespace App\DTOs\Domain\Aperfeicoamentos;

class AperfeicoamentoDTO
{
    public ?int $pk_id_curso = null;
    public ?string $ds_nome_curso = null;

    public static function fromRequest(array $data): self
    {
        $dto = new self();
        $dto->pk_id_curso = !empty($data['pk_id_curso']) ? (int) $data['pk_id_curso'] : null;
        $dto->ds_nome_curso = $data['ds_nome_curso'] ?? null;
        return $dto;
    }

    public function toArray(): array
    {
        $data = [
            'ds_nome_curso' => $this->ds_nome_curso,
        ];

        if ($this->pk_id_curso !== null) {
            $data['pk_id_curso'] = $this->pk_id_curso;
        }

        return $data;
    }
}
