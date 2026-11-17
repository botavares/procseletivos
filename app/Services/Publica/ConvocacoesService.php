<?php

declare(strict_types=1);

namespace App\Services\Publica;

use App\Enums\SituacaoCandidatosEnum;
use App\Repositories\Publica\Interfaces\IConvocacoesRepository;
use App\Services\Publica\Interfaces\IConvocacoesService;

class ConvocacoesService implements IConvocacoesService
{
    public function __construct(
        private readonly IConvocacoesRepository $repository,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function listarPorProcesso(int $processoId): array
    {
        helper('edital');

        $convocacoes = $this->repository->listarPorProcesso($processoId);
        $resultado = [];

        foreach ($convocacoes as $convocacao) {
            $convocados = $this->repository->listarConvocadosPorConvocacao(
                (int) $convocacao['pk_id_convocacao']
            );

            $lista = [];
            foreach ($convocados as $convocado) {
                $candidatoId = (int) $convocado['fk_id_candidato'];
                $cargoId     = (int) $convocacao['fk_id_cargo'];

                $situacao = $this->repository->buscarSituacao($processoId, $cargoId, $candidatoId);
                if (
                    $situacao !== null
                    && (int) $situacao['nm_id_situacao'] === SituacaoCandidatosEnum::DESCLASSIFICADO->value
                ) {
                    continue;
                }

                $classificacao = $this->repository->buscarClassificacao($processoId, $cargoId, $candidatoId);
                $condicao      = (int) $convocado['nm_tipo'] === 1 ? 'TITULAR' : 'SUPLENTE';

                $lista[] = [
                    'c'  => $classificacao['ds_posicao'] ?? '—',
                    'nm' => $convocado['ds_nome'] ?? 'Não informado',
                    'cg' => $convocacao['ds_nome_cargo'] ?? 'Não informado',
                    'cd' => $condicao,
                ];
            }

            $dataPublicacao = !empty($convocacao['dt_data_publicacao'])
                ? date('d/m/Y', strtotime((string) $convocacao['dt_data_publicacao']))
                : null;

            $resultado[] = [
                'n'       => formatarNumeroEdital(
                    ((string) ($convocacao['nm_numero_convocacao'] ?? ''))
                    . ((string) ($convocacao['nm_ano_convocacao'] ?? ''))
                ) ?? '—',
                'dt'      => $dataPublicacao,
                'ed'      => $convocacao['ds_codigo_oficial'] ?? null,
                'link'    => $convocacao['ds_link'] ?? null,
                'arquivo' => $convocacao['ds_arquivo'] ?? null,
                'q'       => count($lista),
                'lista'   => $lista,
            ];
        }

        return $resultado;
    }
}
