<?php

namespace App\DTOs\Domain\CriteriosAdicionais;

class CriterioAdicionalDTO
{
    public ?int $pk_id_criterio = null;
    public ?string $ds_nome_criterio = null;

    public static function fromRequest(array $data): self
    {
        $dto = new self();
        $dto->pk_id_criterio = !empty($data['pk_id_criterio']) ? (int) $data['pk_id_criterio'] : null;
        $dto->ds_nome_criterio = $data['ds_nome_criterio'] ?? null;
        return $dto;
    }

    public function toArray(): array
    {
        $data = [
            'ds_nome_criterio' => $this->ds_nome_criterio,
        ];

        if ($this->pk_id_criterio !== null) {
            $data['pk_id_criterio'] = $this->pk_id_criterio;
        }

        return $data;
    }
}
