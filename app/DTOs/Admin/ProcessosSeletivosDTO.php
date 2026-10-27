<?php

declare(strict_types=1);

namespace App\DTOs\Admin;

readonly class ProcessosSeletivosDTO
{
    public function __construct(
        public ?int $idProcesso,
        public int $idSecretaria,
        public int $numeroProcesso,
        public int $anoProcesso,
        public string $objeto,
        public int $situacao,
        public string $inicioInscricao,
        public string $fimInscricao,
        public ?string $divulgaResultado,
        public int $mesesValidade,
        public int $prorrogado,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            idProcesso: isset($data['pk_id_processo']) ? (int) $data['pk_id_processo'] : null,
            idSecretaria: (int) ($data['fk_id_secretaria'] ?? 0),
            numeroProcesso: (int) ($data['nm_numero_processo'] ?? 0),
            anoProcesso: (int) ($data['nm_ano_processo'] ?? 0),
            objeto: (string) ($data['ds_objeto'] ?? ''),
            situacao: (int) ($data['nm_situacao'] ?? 1),
            inicioInscricao: (string) ($data['dt_inicio_inscricao'] ?? ''),
            fimInscricao: (string) ($data['dt_fim_inscricao'] ?? ''),
            divulgaResultado: isset($data['dt_divulga_resultado']) ? (string) $data['dt_divulga_resultado'] : null,
            mesesValidade: (int) ($data['nm_meses_validade'] ?? 12),
            prorrogado: (int) ($data['nm_prorrogado'] ?? 0),
        );
    }

    public function toArray(): array
    {
        $data = [
            'fk_id_secretaria'     => $this->idSecretaria,
            'nm_numero_processo'     => $this->numeroProcesso,
            'nm_ano_processo'      => $this->anoProcesso,
            'ds_objeto'            => $this->objeto,
            'nm_situacao'          => $this->situacao,
            'dt_inicio_inscricao'  => $this->inicioInscricao,
            'dt_fim_inscricao'     => $this->fimInscricao,
            'dt_divulga_resultado' => $this->divulgaResultado,
            'nm_meses_validade'    => $this->mesesValidade,
            'nm_prorrogado'        => $this->prorrogado,
        ];

        if ($this->idProcesso !== null) {
            $data['pk_id_processo'] = $this->idProcesso;
        }

        return $data;
    }
}
