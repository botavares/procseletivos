<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CriteriosAdicionaisModel;
use App\Models\CadastradosCriteriosAdicionaisModel;
use App\Models\CargosCriteriosAdicionaisModel;

class CriterioAdicionalService
{
    public function __construct(
        private CriteriosAdicionaisModel $criteriosAdicionaisModel,
        private CadastradosCriteriosAdicionaisModel $criteriosAdicionaisCadastrados,
        private CargosCriteriosAdicionaisModel $criteriosAdicionaisCargo,
    ) {}

    public function buscarRequisitos(int $cargoId): array
    {
        return $this->criteriosAdicionaisModel->listarRequisitosCriteriosAdicionais($cargoId);
    }

    public function buscarDadosCadastrados(int $candidatoId, int $cargoId, int $editalId): array
    {
        $criteriosAdicionais = $this->criteriosAdicionaisCadastrados
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->findAll();

        $criteriosAdicionaisIndexados = [];
        $idsCriteriosAdicionais = [];

        if (!empty($criteriosAdicionais)) {
            foreach ($criteriosAdicionais as $ca) {
                $criteriosAdicionaisIndexados[$ca->fk_id_criterio] = $ca->ds_quantidade ?? 0;
                $idsCriteriosAdicionais[] = $ca->fk_id_criterio;
            }
        }

        return [
            'criteriosAdicionais' => $criteriosAdicionais,
            'criteriosAdicionaisIndexados' => $criteriosAdicionaisIndexados,
            'idsCriteriosAdicionais' => $idsCriteriosAdicionais,
        ];
    }

    public function processarFormulario(array $post, int $candidatoId, int $cargoId, int $editalId): void
    {
        $criteriosAdicionais = $this->extrairDoPost($post, $cargoId, $editalId);
        $this->salvar($candidatoId, $criteriosAdicionais, $cargoId, $editalId);
    }

    private function extrairDoPost(array $post, int $cargoId, int $editalId): array
    {
        $criteriosAdicionais = [];
        $lista = $this->criteriosAdicionaisCargo->where('fk_id_cargo', $cargoId)->findAll();

        foreach ($lista as $item) {
            $campo = "criterio{$item->fk_id_criterio}";
            $valor = $post[$campo] ?? null;

            if ($item->ds_tipo_campo === "CHECK") {
                $quantidade = $valor;
            } else {
                $quantidade = is_numeric($valor) ? (int) $valor : 0;
            }

            $criteriosAdicionais[] = [
                'id_edital' => $editalId,
                'id_cargo' => $cargoId,
                'id_criterio_adicional' => $item->fk_id_criterio,
                'ds_quantidade' => $quantidade,
                'ds_multiplicador' => $item->ds_pontuacao_minima,
            ];
        }

        return $criteriosAdicionais;
    }

    private function salvar(int $candidatoId, array $criteriosAdicionais, int $cargoId, int $editalId): void
    {
        $this->criteriosAdicionaisCadastrados
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->delete();

        foreach ($criteriosAdicionais as $ca) {
            $this->criteriosAdicionaisCadastrados->insert([
                'fk_id_cadastrado' => $candidatoId,
                'fk_id_edital' => $ca['id_edital'],
                'fk_id_cargo' => $ca['id_cargo'],
                'fk_id_criterio' => $ca['id_criterio_adicional'],
                'ds_quantidade' => $ca['ds_quantidade'],
                'ds_multiplicador' => $ca['ds_multiplicador'],
            ]);
        }
    }
}
