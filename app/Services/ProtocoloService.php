<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ProtocoloDTO;
use App\Models\ProtocolosModel;
use App\Models\CandidatosModel;
use App\Models\CargosModel;
use App\Repositories\Interfaces\IProtocoloRepository;
use App\Services\Interfaces\IProtocoloService;

class ProtocoloService implements IProtocoloService{
    public function __construct(
        private ProtocolosModel $protocoloModel,
        private CandidatosModel $candidatoModel,
        private CargosModel $cargosModel,
        private IProtocoloRepository $protocoloRepository,
    ) {}

    public function buscarOuGerar(int $candidatoId, int $cargoId, int $editalId): ProtocoloDTO
    {
        $dadosCargo = $this->cargosModel
            ->where('pk_id_cargo', $cargoId)
            ->select('fk_id_secretaria')
            ->first();

        $secretariaId = $dadosCargo->fk_id_secretaria ?? null;

        $protocoloExistente = $this->protocoloModel
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->first();

        $candidato = $this->candidatoModel
            ->where('pk_id_cadastrado', $candidatoId)
            ->first();

        $cpfCandidato = $candidato ? $candidato->ds_cpf : null;

        if ($protocoloExistente) {
            return ProtocoloDTO::fromDados(
                $candidatoId,
                $secretariaId,
                $editalId,
                $cargoId,
                $cpfCandidato,
                $protocoloExistente->ds_protocolo
            );
        }

        return ProtocoloDTO::fromDados(
            $candidatoId,
            $secretariaId,
            $editalId,
            $cargoId,
            $cpfCandidato,
            null
        );
    }

    public function salvar(ProtocoloDTO $dto): bool
    {
        $dados = $dto->toArray();

        $existe = $this->protocoloModel
            ->where('fk_id_cadastrado', $dto->idCadastrado)
            ->where('fk_id_cargo', $dto->idCargo)
            ->where('fk_id_edital', $dto->idEdital)
            ->first();

        if ($existe) {
            return $this->protocoloModel
                ->where('fk_id_cadastrado', $dto->idCadastrado)
                ->where('fk_id_cargo', $dto->idCargo)
                ->where('fk_id_edital', $dto->idEdital)
                ->set($dados)
                ->update() !== false;
        }

        return (bool) $this->protocoloModel->insert($dados);
    }

    public function buscarPorCandidato(int $candidatoId): array
    {
        return $this->protocoloRepository->buscarProtocolosDoCandidato($candidatoId);
    }
}
