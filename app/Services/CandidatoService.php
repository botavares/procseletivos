<?php
namespace App\Services;
use App\DTOs\CandidatoDTO;
use App\Models\CandidatosModel;
use App\Models\DeficienciasModel;
class CandidatoService
{
    protected CandidatosModel $candidatosModel;
    protected DeficienciasModel $deficienciasModel;
    public function __construct()
    {
        $this->candidatosModel = new CandidatosModel();
        $this->deficienciasModel = new DeficienciasModel();
    }
    /**
     * Busca candidato pelo ID
     */
    public function buscarPorId(int $id): ?object
    {
        return $this->candidatosModel->find($id);
    }
    /**
     * Busca candidato pelo CPF
     */
    public function buscarPorCpf(string $cpf): ?object
    {
        $cpfLimpo = preg_replace('/[^0-9]/', '', $cpf);
        return $this->candidatosModel->where('ds_cpf', $cpfLimpo)->first();
    }
    /**
     * Cria ou atualiza candidato
     */
    public function salvar(CandidatoDTO $dto, string $acao): array
    {
        $isCreate = $acao === 'create';
        
        if ($isCreate) {
            $dados = $dto->toArray(true);
            $id = $this->candidatosModel->insert($dados);
            
            if ($id === false) {
                $erros = $this->candidatosModel->errors();
                log_message('error', 'Erro ao inserir candidato: ' . json_encode($erros));
                return ['sucesso' => false, 'erro' => $erros ?: 'Erro ao salvar candidato', 'acao' => 'create'];
            }
            
            return ['sucesso' => true, 'id' => $id, 'acao' => 'create'];

        } else {
            $candidato = $this->buscarPorId($dto->id);
            if (!$candidato) {
                return ['sucesso' => false, 'erro' => 'Candidato não encontrado'];
            }
            
            $dados = $dto->toArray(false);
            $resultado = $this->candidatosModel->update($candidato->pk_id_cadastrado, $dados);
            
            if ($resultado === false) {
                $erros = $this->candidatosModel->errors();
                log_message('error', 'Erro ao atualizar candidato: ' . json_encode($erros));
                return ['sucesso' => false, 'erro' => $erros ?: 'Erro ao atualizar candidato', 'acao' => 'update'];
            }
            
            return ['sucesso' => true, 'id' => $candidato->pk_id_cadastrado, 'acao' => 'update'];
        }
    }
    /**
     * Vincula ID Gov ao candidato existente
     */
    public function vincularGov(int $candidatoId, int $govId): bool
    {
        return $this->candidatosModel->update($candidatoId, ['fk_id_gov' => $govId]);
    }
    /**
     * Verifica status de registro do candidato
     */
    public function verificarStatus(string $cpf): string
    {
        $candidato = $this->buscarPorCpf($cpf);
        return $candidato ? 'registrado' : 'naoregistrado';
    }
    /**
     * Busca deficiência do candidato
     */
    public function buscarDeficiencia(?int $idPne): ?object
    {
        if (!$idPne) {
            return (object) ['pk_id_pne' => 0, 'ds_nome_pne' => ''];
        }
        return $this->deficienciasModel->where('pk_id_pne', $idPne)->first();
    }
    /**
     * Lista todas as deficiências
     */
    public function listarDeficiencias(): array
    {
        return $this->deficienciasModel->orderBy('pk_id_pne', 'asc')->findAll();
    }
}