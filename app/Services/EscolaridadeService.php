<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EscolaridadesModel;
use App\Models\CadastradosEscolaridadesModel;
use App\Models\CargosEscolaridadesModel;

class EscolaridadeService
{
    public function __construct(
        private EscolaridadesModel $escolaridadesModel,
        private CadastradosEscolaridadesModel $escolaridadesCadastrados,
        private CargosEscolaridadesModel $escolaridadesCargo,
    ) {}

    public function buscarRequisitos(int $cargoId): array
    {
        return $this->escolaridadesModel->listarRequisitosEscolaridades($cargoId);
    }

    public function buscarDadosCadastrados(int $candidatoId, int $cargoId, int $editalId): array
    {
        $escolaridades = $this->escolaridadesCadastrados
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->findAll();

        $escolaridadesIndexadas = [];
        $idsEscolaridades = [];

        if (!empty($escolaridades)) {
            foreach ($escolaridades as $esc) {
                $escolaridadesIndexadas[$esc->fk_id_escolaridade] = $esc->ds_quantidade;
                $idsEscolaridades[] = $esc->fk_id_escolaridade;
            }
        }

        return [
            'escolaridades' => $escolaridades,
            'escolaridadesIndexadas' => $escolaridadesIndexadas,
            'idsEscolaridades' => $idsEscolaridades,
        ];
    }

    public function processarFormulario(array $post, int $candidatoId, int $cargoId, int $editalId): void
    {
        $escolaridades = $this->extrairDoPost($post, $cargoId, $editalId);
        $this->salvar($candidatoId, $escolaridades, $cargoId, $editalId);
    }

    private function extrairDoPost(array $post, int $cargoId, int $editalId): array
    {
        $escolaridades = [];
        $lista = $this->escolaridadesCargo->where('fk_id_cargo', $cargoId)->findAll();

        foreach ($lista as $item) {
            $campo = "escolaridade{$item->fk_id_escolaridade}";
            $valor = $post[$campo] ?? null;

            if ($item->ds_tipo_campo === "CHECK") {
                $quantidade = $valor;
            } else {
                $quantidade = is_numeric($valor) ? (int) $valor : 0;
            }

            $escolaridades[] = [
                'id_edital' => $editalId,
                'id_cargo' => $cargoId,
                'id_escolaridade' => $item->fk_id_escolaridade,
                'ds_quantidade' => $quantidade,
                'ds_multiplicador' => $item->ds_pontuacao_minima,
            ];
        }

        return $escolaridades;
    }

    private function salvar(int $candidatoId, array $escolaridades, int $cargoId, int $editalId): void
    {
        $this->escolaridadesCadastrados
            ->where('fk_id_cadastrado', $candidatoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_edital', $editalId)
            ->delete();

        foreach ($escolaridades as $esc) {
            $this->escolaridadesCadastrados->insert([
                'fk_id_cadastrado' => $candidatoId,
                'fk_id_edital' => $esc['id_edital'],
                'fk_id_cargo' => $esc['id_cargo'],
                'fk_id_escolaridade' => $esc['id_escolaridade'],
                'ds_quantidade' => $esc['ds_quantidade'],
                'ds_multiplicador' => $esc['ds_multiplicador'],
            ]);
        }
    }
}
