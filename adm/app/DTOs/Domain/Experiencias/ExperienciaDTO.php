<?php

namespace App\DTOs\Domain\Experiencias;

class ExperienciaDTO
{
    public ?int $pk_id_experiencia = null;
    public ?string $ds_nome_experiencia = null;
    public ?string $ds_tipo_experiencia = null;

    public static function fromRequest(array $data): self
    {
        $dto = new self();
        $dto->pk_id_experiencia = !empty($data['pk_id_experiencia']) ? (int) $data['pk_id_experiencia'] : null;
        $dto->ds_nome_experiencia = $data['ds_nome_experiencia'] ?? null;
        $dto->ds_tipo_experiencia = $data['ds_tipo_experiencia'] ?? null;
        return $dto;
    }

    public function toArray(): array
    {
        $data = [
            'ds_nome_experiencia' => $this->ds_nome_experiencia,
            'ds_tipo_experiencia' => $this->ds_tipo_experiencia,
        ];

        if ($this->pk_id_experiencia !== null) {
            $data['pk_id_experiencia'] = $this->pk_id_experiencia;
        }

        return $data;
    }
}
