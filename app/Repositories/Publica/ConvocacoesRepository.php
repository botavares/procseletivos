<?php

declare(strict_types=1);

namespace App\Repositories\Publica;

use App\Models\Admin\ClassificacaoModel;
use App\Models\Admin\ConvocadosModel;
use App\Models\Admin\ProcessosConvocacaoModel;
use App\Models\Admin\ProcessosDocumentosModel;
use App\Models\Admin\SituacaoModel;
use App\Repositories\Publica\Interfaces\IConvocacoesRepository;

class ConvocacoesRepository implements IConvocacoesRepository
{
    public function __construct(
        private readonly ProcessosConvocacaoModel $processosConvocacaoModel,
        private readonly ProcessosDocumentosModel $processosDocumentosModel,
        private readonly ConvocadosModel $convocadosModel,
        private readonly SituacaoModel $situacaoModel,
        private readonly ClassificacaoModel $classificacaoModel,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function listarPorProcesso(int $processoId): array
    {
        return $this->processosConvocacaoModel
            ->builder()
            ->select([
                'tb_processos_convocacao.pk_id_convocacao',
                'tb_processos_convocacao.fk_id_processo',
                'tb_processos_convocacao.fk_id_documento',
                'tb_processos_convocacao.fk_id_cargo',
                'tb_processos_convocacao.nm_numero_convocacao',
                'tb_processos_convocacao.nm_ano_convocacao',
                'tb_cargos.ds_nome_cargo',
                'tb_processos_documentos.ds_codigo_oficial',
                'tb_processos_documentos.ds_link',
                'tb_processos_documentos.ds_arquivo',
                'tb_processos_documentos.dt_data_publicacao',
            ])
            ->join('tb_cargos', 'tb_cargos.pk_id_cargo = tb_processos_convocacao.fk_id_cargo', 'inner')
            ->join('tb_processos_documentos', 'tb_processos_documentos.pk_id_documento = tb_processos_convocacao.fk_id_documento', 'inner')
            ->where('tb_processos_convocacao.fk_id_processo', $processoId)
            ->where('tb_processos_convocacao.is_deletado IS NULL')
            ->where('tb_processos_documentos.is_deletado IS NULL')
            ->orderBy('tb_processos_convocacao.nm_ano_convocacao', 'DESC')
            ->orderBy('tb_processos_convocacao.nm_numero_convocacao', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * {@inheritDoc}
     */
    public function listarConvocadosPorConvocacao(int $convocacaoId): array
    {
        return $this->convocadosModel
            ->builder()
            ->select([
                'tb_convocados.pk_id_convocado',
                'tb_convocados.fk_id_candidato',
                'tb_convocados.nm_tipo',
                'tb_cadastrados.ds_nome',
            ])
            ->join('tb_cadastrados', 'tb_cadastrados.pk_id_cadastrado = tb_convocados.fk_id_candidato', 'inner')
            ->where('tb_convocados.fk_id_convocacao', $convocacaoId)
            ->where('tb_convocados.is_deletado IS NULL')
            ->orderBy('tb_convocados.nm_tipo', 'DESC')
            ->orderBy('tb_cadastrados.ds_nome', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * {@inheritDoc}
     */
    public function buscarSituacao(int $processoId, int $cargoId, int $candidatoId): ?array
    {
        return $this->situacaoModel->getSituacaoCandidatoCargoProcesso(
            $processoId,
            $cargoId,
            $candidatoId
        );
    }

    /**
     * {@inheritDoc}
     */
    public function buscarClassificacao(int $processoId, int $cargoId, int $candidatoId): ?array
    {
        $classificacao = $this->classificacaoModel
            ->where('fk_id_processo', $processoId)
            ->where('fk_id_cargo', $cargoId)
            ->where('fk_id_candidato', $candidatoId)
            ->first();

        return $classificacao ? (array) $classificacao : null;
    }
}
