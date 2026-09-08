<?php

namespace App\DTOs\Domain\Cargos;

class CargosEscolaridadesDTO
{
    public ?int $pk_id_cargos_escolaridade = null;
    public ?int $fk_id_cargo = null;
    public ?int $fk_id_escolaridade = null;
    public ?int $ds_pontuacao_minima = null;
    public ?int $ds_pontuacao_maxima = null;
    public ?string $ds_tipo_campo = null;
    public ?string $acao = null;

    public static function fromRequest(array $data): self{
        $dto = new self();
        $dto->pk_id_cargos_escolaridade = !empty($data['pk_id_cargos_escolaridade']) ? (int) $data['pk_id_cargos_escolaridade'] : null;
        $dto->fk_id_cargo               = !empty($data['fk_id_cargo']) ? (int) $data['fk_id_cargo'] : null;
        $dto->fk_id_escolaridade         = !empty($data['fk_id_escolaridade']) ? (int) $data['fk_id_escolaridade'] : null;
        $dto->ds_pontuacao_minima      = isset($data['ds_pontuacao_minima']) && $data['ds_pontuacao_minima'] !== '' ? (int) $data['ds_pontuacao_minima'] : null;
        $dto->ds_pontuacao_maxima      = isset($data['ds_pontuacao_maxima']) && $data['ds_pontuacao_maxima'] !== '' ? (int) $data['ds_pontuacao_maxima'] : null;
        $dto->ds_tipo_campo             = $data['ds_tipo_campo'] ?? null;
        $dto->acao                      = $data['acao'] ?? 'create';
        return $dto;
    }

    public function toArray(): array{
        $data = [
            'fk_id_cargo'          => $this->fk_id_cargo,
            'fk_id_escolaridade'    => $this->fk_id_escolaridade,
            'ds_pontuacao_minima' => $this->ds_pontuacao_minima,
            'ds_pontuacao_maxima' => $this->ds_pontuacao_maxima,
            'ds_tipo_campo'        => $this->ds_tipo_campo,
        ];

        if ($this->pk_id_cargos_escolaridade !== null) {
            $data['pk_id_cargos_escolaridade'] = $this->pk_id_cargos_escolaridade;
        }

        return $data;
    }

    public function getAcao(): string
    {
        return $this->acao ?? 'create';
    }
}
