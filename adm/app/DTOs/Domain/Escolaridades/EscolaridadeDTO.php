<?php

namespace App\DTOs\Domain\Escolaridades;

class EscolaridadeDTO
{
    public ?int $pk_id_escolaridade = null;
    public ?string $ds_nome_escolaridade = null;
    public ?string $ds_instrucao = null;
    public ?int $fk_id_nivel = null;

    public static function fromRequest(array $data): self
    {
        $dto = new self();
        $dto->pk_id_escolaridade = !empty($data['pk_id_escolaridade']) ? (int) $data['pk_id_escolaridade'] : null;
        $dto->ds_nome_escolaridade = $data['ds_nome_escolaridade'] ?? null;
        $dto->ds_instrucao = $data['ds_instrucao'] ?? null;
        $dto->fk_id_nivel = !empty($data['fk_id_nivel']) ? (int) $data['fk_id_nivel'] : null;
        return $dto;
    }

    public function toArray(): array
    {
        $data = [
            'ds_nome_escolaridade' => $this->ds_nome_escolaridade,
            'ds_instrucao'         => $this->ds_instrucao,
            'fk_id_nivel'          => $this->fk_id_nivel,
        ];

        if ($this->pk_id_escolaridade !== null) {
            $data['pk_id_escolaridade'] = $this->pk_id_escolaridade;
        }

        return $data;
    }
}
