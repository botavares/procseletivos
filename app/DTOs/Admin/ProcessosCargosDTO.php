<?php

declare(strict_types=1);

namespace App\DTOs\Admin;

class ProcessosCargosDTO
{
    public function __construct(
        public readonly ?int $pkIdProcessoCargo,
        public readonly int $fkIdProcesso,
        public readonly int $fkIdCargo,
        public readonly int $nmVagas,
        public readonly ?int $nmVagasPcd,
        public readonly int $nmCadastroReserva,
        public readonly string $dsRequisitoProvimento,
        public readonly int $nmCargaHoraria,
        public readonly int $nmTipoCargaHoraria,
        public readonly float $nmRemuneracao,
    ) {}

    /**
     * Cria uma instância a partir de um array (ex: resultado de consulta).
     */
    public static function fromArray(array $data): self
    {
        return new self(
            pkIdProcessoCargo: isset($data['pk_id_processo_cargo']) ? (int) $data['pk_id_processo_cargo'] : null,
            fkIdProcesso: (int) $data['fk_id_processo'],
            fkIdCargo: (int) $data['fk_id_cargo'],
            nmVagas: (int) $data['nm_vagas'],
            nmVagasPcd: isset($data['nm_vagas_pcd']) ? (int) $data['nm_vagas_pcd'] : null,
            nmCadastroReserva: (int) $data['nm_cadastro_reserva'],
            dsRequisitoProvimento: (string) $data['ds_requisito_provimento'],
            nmCargaHoraria: (int) $data['nm_carga_horaria'],
            nmTipoCargaHoraria: (int) $data['nm_tipo_carga_horaria'],
            nmRemuneracao: (float) $data['nm_remuneracao'],
        );
    }

    /**
     * Converte o DTO para um array compatível com o Model.
     */
    public function toArray(): array
    {
        return [
            'pk_id_processo_cargo'    => $this->pkIdProcessoCargo,
            'fk_id_processo'          => $this->fkIdProcesso,
            'fk_id_cargo'             => $this->fkIdCargo,
            'nm_vagas'                => $this->nmVagas,
            'nm_vagas_pcd'            => $this->nmVagasPcd,
            'nm_cadastro_reserva'     => $this->nmCadastroReserva,
            'ds_requisito_provimento' => $this->dsRequisitoProvimento,
            'nm_carga_horaria'        => $this->nmCargaHoraria,
            'nm_tipo_carga_horaria'   => $this->nmTipoCargaHoraria,
            'nm_remuneracao'          => $this->nmRemuneracao,
        ];
    }
}
