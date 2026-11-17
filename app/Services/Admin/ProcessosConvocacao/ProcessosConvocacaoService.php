<?php

declare(strict_types=1);

namespace App\Services\Admin\ProcessosConvocacao;

use App\DTOs\Admin\ProcessosConvocacaoDTO;
use App\Models\Admin\ProcessosConvocacaoModel;
use App\Models\ProcessosSeletivosModel;
use App\Services\Admin\LogsService;

class ProcessosConvocacaoService
{
    public function __construct(
        protected ProcessosConvocacaoModel $model,
        protected ProcessosSeletivosModel $processosModel,
        protected LogsService $logsService,
    ) {
    }

    /**
     * Cria uma convocação vinculada a um documento recém-criado.
     * O número da convocação é calculado com base na quantidade
     * de convocações já existentes para o processo seletivo + 1.
     */
    public function criarParaDocumento(int $documentoId, int $processoId, int $cargoId): int
    {
        $processo = $this->processosModel->find($processoId);
        if (!$processo) {
            throw new \RuntimeException('Processo seletivo não encontrado para vincular a convocação.');
        }

        $numeroConvocacao = $this->model->contarPorProcesso($processoId) + 1;
        $anoConvocacao    = (int) ($processo->nm_ano_processo ?? 0);

        $dto = new ProcessosConvocacaoDTO(
            idConvocacao: null,
            fkIdProcesso: $processoId,
            fkIdDocumento: $documentoId,
            nmNumeroConvocacao: $numeroConvocacao,
            nmAnoConvocacao: $anoConvocacao,
            fkIdCargo: $cargoId,
        );

        $id = $this->model->insert($dto->toArray());
        if (!$id) {
            throw new \RuntimeException('Falha ao criar o registro de convocação.');
        }

        $this->logsService->inserirLog(
            'Criar',
            "Convocação {$numeroConvocacao}/{$anoConvocacao} criada para o documento {$documentoId}.",
            'tb_processos_convocacao'
        );

        return (int) $id;
    }

    /**
     * Sincroniza a convocação vinculada a um documento existente.
     * Se já existir, atualiza o processo e o cargo; caso contrário, cria uma nova.
     */
    public function sincronizarPorDocumento(int $documentoId, int $processoId, int $cargoId): int
    {
        $existente = $this->model
            ->where('fk_id_documento', $documentoId)
            ->where('is_deletado IS NULL')
            ->first();

        if ($existente) {
            $this->model->update((int) $existente->pk_id_convocacao, [
                'fk_id_processo' => $processoId,
                'fk_id_cargo'    => $cargoId,
            ]);

            $this->logsService->inserirLog(
                'Alterar',
                "Convocação {$existente->pk_id_convocacao} sincronizada para o documento {$documentoId}.",
                'tb_processos_convocacao'
            );

            return (int) $existente->pk_id_convocacao;
        }

        return $this->criarParaDocumento($documentoId, $processoId, $cargoId);
    }

    /**
     * Busca a convocação vinculada a um documento específico.
     */
    public function buscarPorDocumento(int $documentoId): ?object
    {
        return $this->model
            ->where('fk_id_documento', $documentoId)
            ->where('is_deletado IS NULL')
            ->first() ?: null;
    }

    /**
     * Retorna quantas convocações (não deletadas) o processo possui.
     */
    public function contarPorProcesso(int $processoId): int
    {
        return $this->model->contarPorProcesso($processoId);
    }

    /**
     * Lista as convocações (editais de convocação) vinculadas a um processo.
     */
    public function listarPorProcesso(int $processoId): array
    {
        return $this->model->listarConvocacaoProcesso($processoId);
    }

    /**
     * Busca uma convocação pelo ID, retornando também os dados do documento e cargo.
     */
    public function buscarPorId(int $convocacaoId): ?array
    {
        $resultado = $this->model->listarConvocacaoProcesso(0, $convocacaoId);
        return $resultado[0] ?? null;
    }
}
