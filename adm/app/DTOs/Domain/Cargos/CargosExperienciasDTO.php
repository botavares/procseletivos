<?php

namespace App\DTOs\Domain\Cargos;

class CargosExperienciasDTO
{
    public ?int $pk_id_cargos_experiencia = null;
    public ?int $fk_id_cargo = null;
    public ?int $fk_id_experiencia = null;
    public ?int $ds_quantidade_minima = null;
    public ?int $ds_quantidade_maxima = null;
    public ?float $ds_multiplicador = null;
    public ?string $ds_tipo_campo = null;

    public static function fromRequest(array $data): self
    {
        $dto = new self();
        $dto->pk_id_cargos_experiencia = !empty($data['pk_id_cargos_experiencia']) ? (int) $data['pk_id_cargos_experiencia'] : null;
        $dto->fk_id_cargo               = !empty($data['fk_id_cargo']) ? (int) $data['fk_id_cargo'] : null;
        $dto->fk_id_experiencia         = !empty($data['fk_id_experiencia']) ? (int) $data['fk_id_experiencia'] : null;
        $dto->ds_quantidade_minima      = isset($data['ds_quantidade_minima']) && $data['ds_quantidade_minima'] !== '' ? (int) $data['ds_quantidade_minima'] : null;
        $dto->ds_quantidade_maxima      = isset($data['ds_quantidade_maxima']) && $data['ds_quantidade_maxima'] !== '' ? (int) $data['ds_quantidade_maxima'] : null;
        $dto->ds_multiplicador          = isset($data['ds_multiplicador']) && $data['ds_multiplicador'] !== '' ? (float) $data['ds_multiplicador'] : null;
        $dto->ds_tipo_campo             = $data['ds_tipo_campo'] ?? null;
        return $dto;
    }

    public function toArray(): array
    {
        $data = [
            'fk_id_cargo'          => $this->fk_id_cargo,
            'fk_id_experiencia'    => $this->fk_id_experiencia,
            'ds_quantidade_minima' => $this->ds_quantidade_minima,
            'ds_quantidade_maxima' => $this->ds_quantidade_maxima,
            'ds_multiplicador'     => $this->ds_multiplicador,
            'ds_tipo_campo'        => $this->ds_tipo_campo,
        ];

        if ($this->pk_id_cargos_experiencia !== null) {
            $data['pk_id_cargos_experiencia'] = $this->pk_id_cargos_experiencia;
        }

        return $data;
    }
}
