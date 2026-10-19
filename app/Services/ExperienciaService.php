<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ExperienciasModel;
use App\Models\CadastradosExperienciasModel;
use App\Models\CargosExperienciasModel;

class ExperienciaService
{
    public function __construct(
        private ExperienciasModel $experienciasModel,
        private CadastradosExperienciasModel $experienciasCadastrados,
        private CargosExperienciasModel $experienciasCargo,
    ) {}

    public function buscarRequisitos(int $cargoId): array
    {
        return $this->experienciasModel->listarRequisitosExperiencias($cargoId);
    }

    public function buscarDadosCadastrados(int $candidatoId, int $cargoId, int $editalId): array
    {
        $experiencias = $this->experienciasCadastrados
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->findAll();

        $experienciasSalvas = [];
        foreach ($experiencias as $exp) {
            $experienciasSalvas[$exp->fk_id_experiencia] = $exp->ds_quantidade;
        }

        return [
            'experiencias' => $experiencias,
            'experienciasSalvas' => $experienciasSalvas,
        ];
    }

    public function processarFormulario(array $post, int $candidatoId, int $cargoId, int $editalId): void
    {
        $experiencias = $this->extrairDoPost($post, $cargoId);
        $this->salvar($candidatoId, $experiencias, $cargoId, $editalId);
    }

    private function extrairDoPost(array $post, int $cargoId): array
    {
        $experiencias = [];
        $experienciasCargo = $this->experienciasCargo->where('fk_id_cargo', $cargoId)->findAll();

        foreach ($experienciasCargo as $exp) {
            $campo = "quantidadeExperiencia{$exp->fk_id_experiencia}";
            if (!array_key_exists($campo, $post)) {
                continue;
            }
            $quantidade = is_numeric($post[$campo]) ? (int) $post[$campo] : 0;

            $experiencias[] = [
                'id_edital' => $exp->fk_id_edital ?? $editalId,
                'id_cargo' => $cargoId,
                'id_experiencia' => $exp->fk_id_experiencia,
                'ds_quantidade' => $quantidade,
                'ds_multiplicador' => $exp->ds_pontuacao_minima,
            ];
        }

        return $experiencias;
    }

    private function salvar(int $candidatoId, array $experiencias, int $cargoId, int $editalId): void
    {
        $this->experienciasCadastrados
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->delete();

        foreach ($experiencias as $exp) {
            $this->experienciasCadastrados->insert([
                'fk_id_cadastrado' => $candidatoId,
                'fk_id_cargo' => $exp['id_cargo'],
                'fk_id_edital' => $exp['id_edital'],
                'fk_id_experiencia' => $exp['id_experiencia'],
                'ds_quantidade' => $exp['ds_quantidade'],
                'ds_multiplicador' => $exp['ds_multiplicador'],
            ]);
        }
    }
}
