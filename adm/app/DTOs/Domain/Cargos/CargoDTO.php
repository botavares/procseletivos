<?php

namespace App\DTOs\Domain\Cargos;

class CargoDTO
{
    public ?int $pk_id_cargo = null;
    public ?int $fk_id_secretaria = null;
    public ?string $ds_nome_cargo = null;
    public ?string $ds_carga_horaria = null;
    public ?string $ds_nivel = null;

    public static function fromRequest(array $data): self
    {
        $dto = new self();
        $dto->pk_id_cargo = !empty($data['pk_id_cargo']) ? (int) $data['pk_id_cargo'] : null;
        $dto->fk_id_secretaria = !empty($data['fk_id_secretaria']) ? (int) $data['fk_id_secretaria'] : null;
        $dto->ds_nome_cargo = $data['ds_nome_cargo'] ?? null;
        $dto->ds_carga_horaria = $data['ds_carga_horaria'] ?? null;
        $dto->ds_nivel = $data['ds_nivel'] ?? null;
        return $dto;
    }

    public function toArray(): array
    {
        $data = [
            'fk_id_secretaria' => $this->fk_id_secretaria,
            'ds_nome_cargo'    => $this->ds_nome_cargo,
            'ds_carga_horaria' => $this->ds_carga_horaria,
            'ds_nivel'         => $this->ds_nivel,
        ];

        if ($this->pk_id_cargo !== null) {
            $data['pk_id_cargo'] = $this->pk_id_cargo;
        }

        return $data;
    }
}
