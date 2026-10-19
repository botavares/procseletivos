<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AperfeicoamentosModel;
use App\Models\CadastradosAperfeicoamentosModel;
use App\Models\CargosAperfeicoamentosModel;

class AperfeicoamentoService
{
    public function __construct(
        private AperfeicoamentosModel $aperfeicoamentosModel,
        private CadastradosAperfeicoamentosModel $aperfeicoamentosCadastrados,
        private CargosAperfeicoamentosModel $aperfeicoamentosCargo,
    ) {}

    public function buscarRequisitos(int $cargoId): array
    {
        return $this->aperfeicoamentosModel->listarRequisitosAperfeicoamentos($cargoId);
    }

    public function buscarDadosCadastrados(int $candidatoId, int $cargoId, int $editalId): array
    {
        $aperfeicoamentos = $this->aperfeicoamentosCadastrados
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->findAll();

        $aperfeicoamentosIndexados = [];
        $idsAperfeicoamentos = [];

        if (!empty($aperfeicoamentos)) {
            foreach ($aperfeicoamentos as $ap) {
                $aperfeicoamentosIndexados[$ap->fk_id_curso] = $ap->ds_quantidade ?? 1;
                $idsAperfeicoamentos[] = $ap->fk_id_curso;
            }
        }

        return [
            'aperfeicoamentos' => $aperfeicoamentos,
            'aperfeicoamentosIndexados' => $aperfeicoamentosIndexados,
            'idsAperfeicoamentos' => $idsAperfeicoamentos,
        ];
    }

    public function processarFormulario(array $post, int $candidatoId, int $cargoId, int $editalId): void
    {
        $aperfeicoamentos = $this->extrairDoPost($post, $cargoId, $editalId);
        $this->salvar($candidatoId, $aperfeicoamentos, $cargoId, $editalId);
    }

    private function extrairDoPost(array $post, int $cargoId, int $editalId): array
    {
        $aperfeicoamentos = [];
        $lista = $this->aperfeicoamentosCargo->where('fk_id_cargo', $cargoId)->findAll();

        foreach ($lista as $item) {
            $campo = "aperfeicoamento{$item->fk_id_curso}";
            $valor = $post[$campo] ?? null;

            if ($item->ds_tipo_campo === "CHECK") {
                $quantidade = $valor;
            } else {
                $quantidade = is_numeric($valor) ? (int) $valor : 0;
            }

            $aperfeicoamentos[] = [
                'id_edital' => $editalId,
                'id_cargo' => $cargoId,
                'status' => $item->ds_obrigatorio,
                'id_aperfeicoamento' => $item->fk_id_curso,
                'ds_quantidade' => $quantidade,
                'ds_multiplicador' => $item->ds_pontuacao_minima,
            ];
        }

        return $aperfeicoamentos;
    }

    private function salvar(int $candidatoId, array $aperfeicoamentos, int $cargoId, int $editalId): void
    {
        $this->aperfeicoamentosCadastrados
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->delete();

        foreach ($aperfeicoamentos as $ap) {
            $this->aperfeicoamentosCadastrados->insert([
                'fk_id_cadastrado' => $candidatoId,
                'fk_id_edital' => $ap['id_edital'],
                'fk_id_cargo' => $ap['id_cargo'],
                'fk_id_curso' => $ap['id_aperfeicoamento'],
                'ds_quantidade' => $ap['ds_quantidade'],
                'ds_multiplicador' => $ap['ds_multiplicador'],
            ]);
        }
    }
}
