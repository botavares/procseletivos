<?php

declare(strict_types=1);

namespace App\Services;

use App\Formatters\RecursoFormatter;
use App\Repositories\Interfaces\IRecursoRepository;
use App\Services\Interfaces\ICandidatoRecursoService;

class CandidatoRecursoService implements ICandidatoRecursoService
{
    public function __construct(private IRecursoRepository $recursoRepository) {}

    public function buscarResumoRecursosPorCandidato(int $candidatoId): array
    {
        return $this->recursoRepository->buscarResumoRecursosPorCandidato($candidatoId);
    }

    public function buscarDetalhesRecurso(string $protocolo): array
    {
        $recursos = $this->recursoRepository->buscarDetalhesRecurso($protocolo);

        if (empty($recursos)) {
            return [];
        }

        $primeiro = $recursos[0];
        $numeroEdital = RecursoFormatter::formatarNumeroEdital($primeiro->ds_numero_edital ?? '');

        $campos = [];
        foreach ($recursos as $r) {
            $nomeCampo = $this->recursoRepository->buscarNomeCampo(
                $r->ds_campo_alterado,
                (int) $r->fk_id_campo_alterado
            );
            $tipoCampo = $this->recursoRepository->buscarTipoCampo(
                $r->ds_campo_alterado,
                (int) $r->fk_id_cargo,
                (int) $r->fk_id_campo_alterado
            );
            $status = RecursoFormatter::mapearStatus($r->ds_tipo);

            $campos[] = [
                'id' => $r->pk_id_historico,
                'categoria' => $r->ds_campo_alterado,
                'nome_campo' => $nomeCampo,
                'tipo' => $r->ds_tipo,
                'status' => $status,
                'status_label' => RecursoFormatter::statusLabel($status),
                'valor_antigo' => RecursoFormatter::formatarValor($r->ds_campo_alterado, $tipoCampo, $r->ds_valor_antigo),
                'valor_novo' => RecursoFormatter::formatarValor($r->ds_campo_alterado, $tipoCampo, $r->ds_valor_novo),
                'observacao' => $r->ds_observacao ?? '',
            ];
        }

        return [
            'protocolo' => $protocolo,
            'numero_edital' => $numeroEdital,
            'nome_cargo' => $primeiro->ds_nome_cargo ?? '',
            'fk_id_edital' => $primeiro->fk_id_edital,
            'fk_id_cargo' => $primeiro->fk_id_cargo,
            'campos' => $campos,
        ];
    }
}
