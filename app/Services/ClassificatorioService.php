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

    public function buscarRequisitos(int $cargoId): array
    {
        return [
            'experienciasClassificatorias' => $this->experienciaService->buscarRequisitos($cargoId),
            'escolaridadesClassificatorias' => $this->escolaridadeService->buscarRequisitos($cargoId),
            'aperfeicoamentosClassificatorios' => $this->aperfeicoamentoService->buscarRequisitos($cargoId),
            'criteriosAdicionaisClassificatorios' => $this->criterioAdicionalService->buscarRequisitos($cargoId),
        ];
    }

    public function buscarDadosCadastrados(int $candidatoId, int $cargoId, int $editalId): array
    {
        $experiencias = $this->experienciaService->buscarDadosCadastrados($candidatoId, $cargoId, $editalId);
        $escolaridades = $this->escolaridadeService->buscarDadosCadastrados($candidatoId, $cargoId, $editalId);
        $aperfeicoamentos = $this->aperfeicoamentoService->buscarDadosCadastrados($candidatoId, $cargoId, $editalId);
        $criterios = $this->criterioAdicionalService->buscarDadosCadastrados($candidatoId, $cargoId, $editalId);

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

    public function processarFormulario(array $post, int $candidatoId, int $cargoId, int $editalId): bool
    {
        $this-\u003edb-\u003etransStart();

        try {
            $this-\u003eexperienciaService-\u003eprocessarFormulario($post, $candidatoId, $cargoId, $editalId);
            $this-\u003eescolaridadeService-\u003eprocessarFormulario($post, $candidatoId, $cargoId, $editalId);
            $this-\u003eaperfeicoamentoService-\u003eprocessarFormulario($post, $candidatoId, $cargoId, $editalId);
            $this-\u003ecriterioAdicionalService-\u003eprocessarFormulario($post, $candidatoId, $cargoId, $editalId);

            $this-\u003edb-\u003etransComplete();

            return true;
        } catch (\Exception $e) {
            $this-\u003edb-\u003etransRollback();
            log_message('error', 'Erro ao processar formulário classificatório: ' . $e-\u003egetMessage());
            throw $e;
        }
    }
}
