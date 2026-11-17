<?php

declare(strict_types=1);

namespace App\Services\ProcessosDocumentos;

use App\DTOs\Admin\ProcessosDocumentosDTO;
use App\Enums\Admin\TiposDocumentosEnum;
use App\Models\Admin\ProcessosDocumentosModel;
use App\Models\ProcessosSeletivosModel;
use App\Services\Admin\LogsService;
use App\Services\SituacaoProcessosService;

class ProcessosDocumentosService implements IProcessosDocumentos
{
    protected ProcessosSeletivosModel $processosModel;
    protected SituacaoProcessosService $situacaoService;

    public function __construct(
        protected ProcessosDocumentosModel $model,
        protected LogsService $logsService,
        ?ProcessosSeletivosModel $processosModel = null,
        ?SituacaoProcessosService $situacaoService = null,
    ) {
        $this->processosModel = $processosModel ?? new ProcessosSeletivosModel();
        $this->situacaoService = $situacaoService ?? new SituacaoProcessosService();
    }

    public function criar(ProcessosDocumentosDTO $dto): int
    {
        if ($dto->tipo === TiposDocumentosEnum::RETIFICACAO->value) {
            $dto = $this->aplicarSubstituicaoEdital($dto);
        }

        $id = $this->model->insert($dto->toArray());

        if ($id) {
            $this->logsService->inserirLog(
                'Criar',
                'Documento criado: ' . $dto->numeroDocumento . '/' . $dto->anoDocumento . ' (Tipo: ' . $dto->tipo . ')',
                'tb_processos_documentos'
            );

            $this->sincronizarDatasProcesso($dto);
        }

        return (int) $id;
    }

    public function atualizar(int $id, ProcessosDocumentosDTO $dto): bool
    {
        $anterior = $this->model->find($id);
        if (!$anterior) {
            return false;
        }

        if ($dto->tipo === TiposDocumentosEnum::RETIFICACAO->value) {
            $dto = $this->aplicarSubstituicaoEdital($dto);
        }

        $atual = $dto->toArray();
        $resultado = $this->model->update($id, $atual);

        if ($resultado) {
            $this->sincronizarDatasProcesso($dto);
            $camposAlterados = [];
            foreach ($atual as $campo => $valor) {
                if (!property_exists($anterior, $campo)) {
                    continue;
                }
                $valorAnterior = $anterior->{$campo};
                $valorNovo     = $valor;

                $a = $valorAnterior === null ? null : (string) $valorAnterior;
                $b = $valorNovo === null ? null : (string) $valorNovo;

                if ($a !== $b) {
                    $camposAlterados[] = $campo;
                }
            }

            $this->logsService->inserirLog(
                'Alterar',
                'Documento ID ' . $id . ' alterado. Campos: ' . implode(', ', $camposAlterados),
                'tb_processos_documentos'
            );
        }

        return (bool) $resultado;
    }

    public function deletar(int $id): bool
    {
        $resultado = $this->model->delete($id);

        if ($resultado) {
            $this->logsService->inserirLog(
                'Deletar',
                'Documento ID ' . $id . ' soft deleted',
                'tb_processos_documentos'
            );
        }

        return (bool) $resultado;
    }

    /**
     * Sincroniza as datas de divulgação de resultado e homologação do processo
     * com base no tipo do documento recém-salvo e na data de publicação informada.
     * Após atualizar as datas, recalcula e persiste a situação do processo.
     */
    protected function sincronizarDatasProcesso(ProcessosDocumentosDTO $dto): void
    {
        if (!in_array($dto->tipo, [
            TiposDocumentosEnum::RESULTADO_PARCIAL->value,
            TiposDocumentosEnum::HOMOLOGACAO->value,
        ], true)) {
            return;
        }

        $idProcesso = $dto->processoReferencia;
        if ($idProcesso <= 0) {
            return;
        }

        $processo = $this->processosModel->find($idProcesso);
        if (!$processo) {
            return;
        }

        $dataPublicacao = $this->extrairDataPublicacao($dto->dataPublicacao);

        $dadosAtualizacao = [];
        if ($dto->tipo === TiposDocumentosEnum::RESULTADO_PARCIAL->value) {
            $dadosAtualizacao['dt_divulga_resultado'] = $dataPublicacao;
        } elseif ($dto->tipo === TiposDocumentosEnum::HOMOLOGACAO->value) {
            $dadosAtualizacao['dt_data_homologacao'] = $dataPublicacao;
        }

        if (empty($dadosAtualizacao)) {
            return;
        }

        $this->processosModel->update($idProcesso, $dadosAtualizacao);

        // Recarrega o processo após a atualização para recalcular a situação
        $processoAtualizado = $this->processosModel->find($idProcesso);
        if ($processoAtualizado) {
            $situacaoCalculada = $this->situacaoService->calcularSituacao($processoAtualizado);
            $this->processosModel->update($idProcesso, ['nm_situacao' => $situacaoCalculada->value]);
        }
    }

    /**
     * Converte a data de publicação (Y-m-d H:i:s) no formato Y-m-d H:i:s
     * mantendo o horário informado ou, quando ausente, 00:00:00.
     */
    private function extrairDataPublicacao(?string $dataPublicacao): ?string
    {
        if (empty($dataPublicacao)) {
            return null;
        }

        $data = \DateTime::createFromFormat('Y-m-d H:i:s', $dataPublicacao);
        if (!$data) {
            return null;
        }

        return $data->format('Y-m-d H:i:s');
    }

    public function buscarPorId(int $id): ?object
    {
        return $this->model->find($id) ?: null;
    }

    public function listarTodos(): array
    {
        return $this->model->listarTodos();
    }

    public function listarPorSecretaria(int $idSecretaria): array
    {
        return $this->model->porSecretaria($idSecretaria);
    }

    public function listarPorTipo(int $tipo): array
    {
        return $this->model->porTipo($tipo);
    }

    public function listarPorNumeroProcesso(int $numero, int $ano): array
    {
        return $this->model->porNumeroProcesso($numero, $ano);
    }

    public function listarPorNumeroDocumentoEAno(int $numero, int $ano): array
    {
        return $this->model->porNumeroDocumentoEAno($numero, $ano);
    }

    public function listarPorFaixaDeTempo(string $dataInicio, string $dataFim): array
    {
        return $this->model->porFaixaDeTempo($dataInicio, $dataFim);
    }

    public function listarPorProcessoId(int $idProcesso): array
    {
        return $this->model->porProcessoId($idProcesso);
    }

    /**
     * Lista documentos do processo enriquecidos para exibição no grid.
     * Adiciona:
     *  - tipo_label: label traduzido do enum de tipo;
     *  - vigente_label: label a ser exibido na coluna Vigente
     *    (para Resultado Parcial mostra "Mais recente" / "-";
     *     para documentos substituídos mostra "substituído em dd/mm/YYYY";
     *     para demais tipos mantém Sim/Não com base em nm_versao_vigente).
     *  - vigente_classe: classe do badge para estilização na view.
     *
     * Ordena os documentos para que o arquivo vigente (edital de abertura
     * ou retificação mais recente) apareça acima dos demais.
     */
    public function listarPorProcessoIdParaGrid(int $idProcesso): array
    {
        $documentos = $this->model->porProcessoId($idProcesso);

        // Mapeia qual retificação substituiu cada documento antigo
        $substituidoPor = [];
        foreach ($documentos as $doc) {
            $idRetificado = (int) ($doc->nm_documento_retificado ?? 0);
            if ($idRetificado > 0) {
                $substituidoPor[$idRetificado] = [
                    'codigo_oficial' => $doc->ds_codigo_oficial ?? null,
                    'data_substituicao' => $doc->dt_data_publicacao ?? $doc->dt_data_cadastro ?? null,
                ];
            }
        }

        // Resultados parciais - mais recente
        $resultadosParciais = array_values(array_filter(
            $documentos,
            fn($d) => (int) ($d->nm_tipo ?? 0) === TiposDocumentosEnum::RESULTADO_PARCIAL->value
        ));

        $maisRecenteResultadoParcialId = null;
        if (!empty($resultadosParciais)) {
            $maisRecenteResultadoParcialId = (int) max(array_map(
                fn($d) => (int) $d->pk_id_documento,
                $resultadosParciais
            ));
        }

        foreach ($documentos as $doc) {
            $enum = TiposDocumentosEnum::tryFrom((int) $doc->nm_tipo);
            $doc->tipo_label = $enum?->label() ?? 'Desconhecido';

            $idDoc          = (int) $doc->pk_id_documento;
            $foiSubstituido = isset($substituidoPor[$idDoc]) || !empty($doc->dt_data_substituicao);
            $dataSubstituicao = $doc->dt_data_substituicao ?? null;

            if ((int) $doc->nm_tipo === TiposDocumentosEnum::RESULTADO_PARCIAL->value) {
                $doc->vigente_label  = ($idDoc === $maisRecenteResultadoParcialId)
                    ? 'Mais recente'
                    : '-';
                $doc->vigente_classe = ($idDoc === $maisRecenteResultadoParcialId)
                    ? 'badge-info'
                    : 'text-muted';
            } elseif ($foiSubstituido && $dataSubstituicao) {
                $retificacao = $substituidoPor[$idDoc] ?? null;
                $codigoRetificacao = $retificacao['codigo_oficial'] ?? null;

                $doc->vigente_label  = 'substituido em ' . date('d/m/Y', strtotime((string) $dataSubstituicao));
                if ($codigoRetificacao) {
                    $doc->vigente_label .= ' pela retificação: ' . $codigoRetificacao;
                }
                $doc->vigente_classe = 'text-muted';
                $doc->retificacao_codigo = $codigoRetificacao;
                $doc->retificacao_link   = $codigoRetificacao
                    ? url_diario_oficial($codigoRetificacao)
                    : null;
            } else {
                $doc->vigente_label  = ((int) ($doc->nm_versao_vigente ?? 0) === 1) ? 'Sim' : 'Não';
                $doc->vigente_classe = ((int) ($doc->nm_versao_vigente ?? 0) === 1) ? 'badge-success' : 'badge-secondary';
            }
        }

        // Ordena para que os documentos vigentes fiquem acima dos substituídos
        usort($documentos, function ($a, $b) use ($substituidoPor) {
            $aVigente     = ((int) ($a->nm_versao_vigente ?? 0) === 1);
            $bVigente     = ((int) ($b->nm_versao_vigente ?? 0) === 1);
            $aSubstituido = isset($substituidoPor[(int) $a->pk_id_documento]) || !empty($a->dt_data_substituicao);
            $bSubstituido = isset($substituidoPor[(int) $b->pk_id_documento]) || !empty($b->dt_data_substituicao);

            if ($aVigente && !$bVigente) {
                return -1;
            }
            if (!$aVigente && $bVigente) {
                return 1;
            }

            if ($aSubstituido && !$bSubstituido) {
                return 1;
            }
            if (!$aSubstituido && $bSubstituido) {
                return -1;
            }

            // Mantém a ordenação original por tipo e número como critério final
            $tipoA = (int) ($a->nm_tipo ?? 0);
            $tipoB = (int) ($b->nm_tipo ?? 0);
            if ($tipoA !== $tipoB) {
                return $tipoA <=> $tipoB;
            }

            return (int) ($b->pk_id_documento ?? 0) <=> (int) ($a->pk_id_documento ?? 0);
        });

        return $documentos;
    }

    public function existeEditalAbertura(int $idProcesso, ?int $excetoId = null): bool
    {
        return $this->model->existeEditalAbertura($idProcesso, $excetoId);
    }

    /**
     * Para retificações, localiza o edital de abertura do processo, grava a data
     * de substituição no edital antigo, retira a condição de vigente do edital
     * antigo e devolve um DTO com a retificação marcada como vigente.
     *
     * Garante que apenas uma retificação fique vigente por processo: se já
     * existir uma retificação vigente, ela perde a condição de vigente.
     */
    private function aplicarSubstituicaoEdital(ProcessosDocumentosDTO $dto): ProcessosDocumentosDTO
    {
        $edital = $this->model->buscarEditalAberturaPorProcesso($dto->processoReferencia);

        if (!$edital) {
            throw new \RuntimeException('Não existe Edital de Abertura para este processo. Cadastre-o antes de criar uma retificação.');
        }

        $dataAtual = date('Y-m-d H:i:s');
        $this->model->update((int) $edital->pk_id_documento, [
            'dt_data_substituicao' => $dataAtual,
            'nm_versao_vigente'    => 0,
        ]);

        // Garante apenas uma retificação vigente por processo
        $retificacaoVigente = $this->model->buscarRetificacaoVigente(
            $dto->processoReferencia,
            $dto->idDocumento
        );

        if ($retificacaoVigente) {
            $this->model->update((int) $retificacaoVigente->pk_id_documento, [
                'nm_versao_vigente'    => 0,
                'dt_data_substituicao' => $dataAtual,
            ]);
        }

        return new ProcessosDocumentosDTO(
            idDocumento:      $dto->idDocumento,
            secretaria:       $dto->secretaria,
            processoReferencia: $dto->processoReferencia,
            tipo:             $dto->tipo,
            numeroDocumento:  $dto->numeroDocumento,
            anoDocumento:     $dto->anoDocumento,
            codigoOficial:    $dto->codigoOficial,
            link:             $dto->link,
            arquivo:          $dto->arquivo,
            versaoVigente:    1,
            documentoRetificado: (int) $edital->pk_id_documento,
            dataPublicacao:   $dataAtual,
            dataSubstituicao: null,
        );
    }
}
