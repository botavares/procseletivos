<?php

declare(strict_types=1);

namespace App\DTOs\Admin;

readonly class ProcessosConvocacaoDTO
{
    public function __construct(
        public ?int $idConvocacao,
        public int $fkIdProcesso,
        public int $fkIdDocumento,
        public int $nmNumeroConvocacao,
        public int $nmAnoConvocacao,
        public int $fkIdCargo,
    ) {
        if ($this->fkIdProcesso <= 0) {
            throw new \InvalidArgumentException('O processo seletivo é obrigatório para a convocação.');
        }

        if ($this->fkIdDocumento <= 0) {
            throw new \InvalidArgumentException('O documento vinculado é obrigatório para a convocação.');
        }

        if ($this->fkIdCargo <= 0) {
            throw new \InvalidArgumentException('O cargo vinculado é obrigatório para a convocação.');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            idConvocacao: isset($data['pk_id_convocacao']) ? (int) $data['pk_id_convocacao'] : null,
            fkIdProcesso: (int) ($data['fk_id_processo'] ?? 0),
            fkIdDocumento: (int) ($data['fk_id_documento'] ?? 0),
            nmNumeroConvocacao: (int) ($data['nm_numero_convocacao'] ?? 0),
            nmAnoConvocacao: (int) ($data['nm_ano_convocacao'] ?? 0),
            fkIdCargo: (int) ($data['fk_id_cargo'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'pk_id_convocacao'    => $this->idConvocacao,
            'fk_id_processo'      => $this->fkIdProcesso,
            'fk_id_documento'     => $this->fkIdDocumento,
            'nm_numero_convocacao' => $this->nmNumeroConvocacao,
            'nm_ano_convocacao'   => $this->nmAnoConvocacao,
            'fk_id_cargo'         => $this->fkIdCargo,
        ];
    }
}
