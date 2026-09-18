<?php
namespace App\Services\Candidatos;

use App\Models\RecursosHistoricoModel;
use App\Models\ProtocolosModel;
use App\Models\CandidatosModel;
use App\Models\EditaisModel;
use App\Models\CargosModel;

class RecursosConsultaService
{
    protected RecursosHistoricoModel $historicoModel;
    protected ProtocolosModel $protocolosModel;
    protected CandidatosModel $candidatosModel;
    protected EditaisModel $editaisModel;
    protected CargosModel $cargosModel;

    public function __construct()
    {
        $this->historicoModel = new RecursosHistoricoModel();
        $this->protocolosModel = new ProtocolosModel();
        $this->candidatosModel = new CandidatosModel();
        $this->editaisModel = new EditaisModel();
        $this->cargosModel = new CargosModel();
    }

    /**
     * Busca indeferimentos existentes para um candidato/edital/cargo
     */
    public function buscarIndeferimentos(int $edital, int $cargo, int $candidato): array
    {
        $resultados = $this->historicoModel
            ->where('fk_id_edital', $edital)
            ->where('fk_id_cargo', $cargo)
            ->where('fk_id_candidato', $candidato)
            ->where('ds_tipo', 'indeferido')
            ->findAll();

        $map = [];
        foreach ($resultados as $item) {
            $map[$item->ds_campo_alterado][$item->fk_id_campo_alterado] = [
                'marcado'    => true,
                'observacao' => $item->ds_observacao ?? '',
            ];
        }
        return $map;
    }

    /**
     * Busca protocolos de um candidato pelo CPF
     */
    public function buscarProtocolosPorCpf(string $cpf): array
    {
        if (!method_exists($this->protocolosModel, 'getProtocoloByCpf')) {
            // Fallback se o método não existir no model do adm
            return $this->protocolosModel
                ->where('ds_cpf_cadastrado', $cpf)
                ->findAll();
        }
        return $this->protocolosModel->getProtocoloByCpf($cpf);
    }

    /**
     * Busca dados básicos de um candidato pelo CPF
     */
    public function buscarCandidatoPorCpf(string $cpf): ?object
    {
        return $this->candidatosModel->where('ds_cpf', $cpf)->first();
    }

    /**
     * Busca dados de um edital
     */
    public function buscarDadosEdital(int $id): ?object
    {
        return $this->editaisModel->getEdital($id);
    }

    /**
     * Busca dados de um cargo
     */
    public function buscarDadosCargo(int $id): ?object
    {
        return $this->cargosModel->getCargo($id);
    }

    /**
     * Busca candidatos por edital e cargo
     */
    public function buscarCandidatosPorEditalCargo(int $edital, int $cargo): array
    {
        return $this->candidatosModel->getCandidatosPorEditalCargo($edital, $cargo);
    }

    /**
     * Busca candidatos com recursos por edital e cargo
     */
    public function buscarCandidatosComRecursosPorEditalCargo(int $edital, int $cargo): array
    {
        return $this->candidatosModel->getCandidatosComRecursosPorEditalCargo($edital, $cargo);
    }
    public function listarEditais(): array
    {
        return $this->editaisModel->findAll();
    }

    /**
     * Lista todos os cargos
     */
    public function listarCargos(): array
    {
        return $this->cargosModel->findAll();
    }

    /**
     * Busca histórico de recursos com filtros
     */
    public function buscarHistorico(array $filtros): array
    {
        return $this->historicoModel->listarHistorico($filtros);
    }

    /**
     * Busca um recurso específico pelo ID
     */
    public function buscarRecursoPorId(int $id): ?object
    {
        return $this->historicoModel->obterRecurso($id);
    }

    /**
     * Busca registros pelo número de protocolo
     */
    public function buscarRegistrosPorProtocolo(string $protocolo): array
    {
        return $this->historicoModel->listarPorProtocolo($protocolo);
    }
}
