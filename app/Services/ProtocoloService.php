<?php
namespace App\Services;
use App\DTOs\ProtocoloDTO;
use App\Models\ProtocolosModel;
use App\Models\CandidatosModel;
class ProtocoloService
{
    protected ProtocolosModel $protocoloModel;
    protected CandidatosModel $candidatoModel;
    public function __construct()
    {
        $this->protocoloModel = new ProtocolosModel();
        $this->candidatoModel = new CandidatosModel();
    }
    /**
     * Busca protocolo existente ou gera novo
     */
    public function buscarOuGerar(int $candidatoId, int $cargoId, int $editalId, ?int $secretariaId = null): ProtocoloDTO
    {
            

        $protocoloExistente = $this->protocoloModel
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->first();
        $candidato = $this->candidatoModel->where('pk_id_cadastrado', $candidatoId)->first();
        $cpfCandidato = $candidato ? $candidato->ds_cpf : null;
        
        if($protocoloExistente){
            
            return ProtocoloDTO::fromDados(
                $candidatoId,
                $secretariaId,
                $editalId,
                $cargoId,
                $cpfCandidato,
                $protocoloExistente->ds_protocolo
            );
        }else{
            return ProtocoloDTO::fromDados(
                $candidatoId,
                $secretariaId,
                $editalId,
                $cargoId,
                $cpfCandidato,
                $protocoloExistente->ds_protocolo ?? null
        );}
        
    }
    /**
     * Salva ou atualiza protocolo
     */
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
    /**
     * Busca protocolos do candidato
     */
    public function buscarPorCandidato(int $candidatoId): array
    {
        return $this->protocoloModel->protocolosdoCandidato('fk_id_cadastrado', $candidatoId);
    }
}