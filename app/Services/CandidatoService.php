<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CandidatoDTO;
use App\Models\CandidatosModel;
use App\Models\DeficienciasModel;
use App\Services\Interfaces\ICandidatoService;

class CandidatoService implements ICandidatoService
{
    public function __construct(
        private CandidatosModel $candidatosModel,
        private DeficienciasModel $deficienciasModel,
    ) {}

    public function buscarPorId(int $id): ?object
    {
        return $this->candidatosModel->find($id);
    }

    public function buscarPorCpf(string $cpf): ?object
    {
        $cpfLimpo = preg_replace('/[^0-9]/', '', $cpf);
        return $this->candidatosModel->where('ds_cpf', $cpfLimpo)->first();
    }

    public function salvar(CandidatoDTO $dto, string $acao, ?object $request = null): array
    {
        $isCreate = $acao === 'create';
        $dados = $dto->toArray();

        if ($isCreate) {
            $dados['ds_data_cadastro'] = date('Y-m-d');
            $dados['ds_hora_cadastro'] = date('H:i:s');
            $dados['ds_ip_cadastro'] = $this->obterIp($request);

            $id = $this->candidatosModel->insert($dados);

            if ($id === false) {
                $erros = $this->candidatosModel->errors();
                log_message('error', 'Erro ao inserir candidato: ' . json_encode($erros));
                return ['sucesso' => false, 'erro' => $erros ?: 'Erro ao salvar candidato', 'acao' => 'create'];
            }

            return ['sucesso' => true, 'id' => $id, 'acao' => 'create'];
        }

        $candidato = $this->buscarPorId($dto->id);
        if (!$candidato) {
            return ['sucesso' => false, 'erro' => 'Candidato não encontrado'];
        }

        $dados['ds_data_alteracao'] = date('Y-m-d');
        $dados['ds_hora_alteracao'] = date('H:i:s');

        $resultado = $this->candidatosModel->update($candidato->pk_id_cadastrado, $dados);

        if ($resultado === false) {
            $erros = $this->candidatosModel->errors();
            log_message('error', 'Erro ao atualizar candidato: ' . json_encode($erros));
            return ['sucesso' => false, 'erro' => $erros ?: 'Erro ao atualizar candidato', 'acao' => 'update'];
        }

        return ['sucesso' => true, 'id' => $candidato->pk_id_cadastrado, 'acao' => 'update'];
    }

    private function obterIp(?object $request): ?string
    {
        if ($request !== null && method_exists($request, 'getIPAddress')) {
            return $request->getIPAddress();
        }

        return $_SERVER['REMOTE_ADDR'] ?? null;
    }

    public function vincularGov(int $candidatoId, int $govId): bool
    {
        return $this->candidatosModel->update($candidatoId, ['fk_id_gov' => $govId]);
    }

    public function verificarStatus(string $cpf): string
    {
        $candidato = $this->buscarPorCpf($cpf);
        return $candidato ? 'registrado' : 'naoregistrado';
    }

    public function buscarDeficiencia(?int $idPne): ?object
    {
        if (!$idPne) {
            return (object) ['pk_id_pne' => 0, 'ds_nome_pne' => ''];
        }

        return $this->deficienciasModel->where('pk_id_pne', $idPne)->first();
    }

    public function listarDeficiencias(): array
    {
        return $this->deficienciasModel->orderBy('pk_id_pne', 'asc')->findAll();
    }
}
