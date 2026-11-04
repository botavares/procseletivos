<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CargosModel;
use App\Services\Interfaces\IClassificatorioService;
use CodeIgniter\Database\BaseConnection;

class ClassificatorioService implements IClassificatorioService
{
    public function __construct(
        private CargosModel $cargosModel,
        private ExperienciaService $experienciaService,
        private EscolaridadeService $escolaridadeService,
        private AperfeicoamentoService $aperfeicoamentoService,
        private CriterioAdicionalService $criterioAdicionalService,
        private BaseConnection $db,
    ) {}

    public function buscarDadosCargo(int $cargoId): ?object
    {
        return $this->cargosModel->where('pk_id_cargo', $cargoId)->first();
    }

    private function resolverEditalPorProcesso(int $processoId): ?int
    {
        $row = $this->db->table('tb_processos')
            ->select('nm_numero_processo, nm_ano_processo')
            ->where('pk_id_processo', $processoId)
            ->where("(is_deletado IS NULL OR is_deletado = '0000-00-00 00:00:00')", null, false)
            ->get()
            ->getRow();

        if (!$row) {
            return null;
        }

        $numeroCompleto = ((int) $row->nm_numero_processo * 10000) + (int) $row->nm_ano_processo;

        $edital = $this->db->table('tb_editais')
            ->select('pk_id_edital')
            ->where('ds_numero_edital', $numeroCompleto)
            ->get()
            ->getRow();

        return $edital ? (int) $edital->pk_id_edital : null;
    }

    public function buscarRequisitos(int $cargoId): array
    {
        return [
            'experienciasClassificatorias' => $this->experienciaService->buscarRequisitos($cargoId),
            'escolaridadesClassificatorias' => $this->escolaridadeService->buscarRequisitos($cargoId),
            'aperfeicoamentosClassificatorios' => $this->aperfeicoamentoService->buscarRequisitos($cargoId),
            'criteriosAdicionaisClassificatorios' => $this->criterioAdicionalService->buscarRequisitos($cargoId),
        ];
    }

    public function buscarDadosCadastrados(int $candidatoId, int $cargoId, int $processoId): array
    {
        $editalId = $this->resolverEditalPorProcesso($processoId);

        $experiencias = $this->experienciaService->buscarDadosCadastrados($candidatoId, $cargoId, $processoId);
        $escolaridades = $this->escolaridadeService->buscarDadosCadastrados($candidatoId, $cargoId, $editalId ?? 0);
        $aperfeicoamentos = $this->aperfeicoamentoService->buscarDadosCadastrados($candidatoId, $cargoId, $editalId ?? 0);
        $criterios = $this->criterioAdicionalService->buscarDadosCadastrados($candidatoId, $cargoId, $editalId ?? 0);

        return array_merge(
            $experiencias,
            $escolaridades,
            $aperfeicoamentos,
            $criterios,
            [
                'experienciasSalvas' => $experiencias['experienciasSalvas'] ?? [],
                'idsEscolaridades' => $escolaridades['idsEscolaridades'] ?? [],
                'idsAperfeicoamentos' => $aperfeicoamentos['idsAperfeicoamentos'] ?? [],
                'idsCriteriosAdicionais' => $criterios['idsCriteriosAdicionais'] ?? [],
                'escolaridadesIndexadas' => $escolaridades['escolaridadesIndexadas'] ?? [],
                'criteriosAdicionaisIndexados' => $criterios['criteriosAdicionaisIndexados'] ?? [],
                'aperfeicoamentosIndexados' => $aperfeicoamentos['aperfeicoamentosIndexados'] ?? [],
            ]
        );
    }

    public function processarFormulario(array $post, int $candidatoId, int $cargoId, int $processoId): bool
    {
        $editalId = $this->resolverEditalPorProcesso($processoId);

        $this->db->transStart();

        try {
            $this->experienciaService->processarFormulario($post, $candidatoId, $cargoId, $processoId);
            $this->escolaridadeService->processarFormulario($post, $candidatoId, $cargoId, $editalId ?? 0);
            $this->aperfeicoamentoService->processarFormulario($post, $candidatoId, $cargoId, $editalId ?? 0);
            $this->criterioAdicionalService->processarFormulario($post, $candidatoId, $cargoId, $editalId ?? 0);

            $this->db->transComplete();

            return true;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Erro ao processar formulário classificatório: ' . $e->getMessage());
            throw $e;
        }
    }
}
