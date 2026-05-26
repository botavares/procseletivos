<?php
namespace App\DTOs;
class ProtocoloDTO
{
    public ?int $idCadastrado;
    public ?int $idSecretaria;
    public ?int $idEdital;
    public ?int $idCargo;
    public string $cpfCadastrado;
    public string $protocolo;
    public string $dataProtocolo;
    public string $horaProtocolo;
    public static function fromDados(
        int $idCadastrado,
        ?int $idSecretaria,
        int $idEdital,
        int $idCargo,
        ?string $cpf = null,
        ?string $protocoloExistente = null
    ): self {
        $dto = new self();
        $dto->idCadastrado = $idCadastrado;
        $dto->idSecretaria = $idSecretaria;
        $dto->idEdital = $idEdital;
        $dto->idCargo = $idCargo;
        $dto->cpfCadastrado = preg_replace('/[^0-9]/', '', $cpf);
        $dto->protocolo = $protocoloExistente ?? $idSecretaria . $idCargo . $idEdital . mt_rand(100000, 999999);
        $dto->dataProtocolo = date('Y-m-d');
        $dto->horaProtocolo = date('H:i:s');
        return $dto;
    }
    public function toArray(): array
    {
        return [
            'fk_id_cadastrado' => $this->idCadastrado,
            'fk_id_secretaria' => $this->idSecretaria,
            'fk_id_edital' => $this->idEdital,
            'fk_id_cargo' => $this->idCargo,
            'ds_cpf_cadastrado' => $this->cpfCadastrado,
            'ds_protocolo' => $this->protocolo,
            'ds_data_protocolo' => $this->dataProtocolo,
            'ds_hora_protocolo' => $this->horaProtocolo,
        ];
    }
}