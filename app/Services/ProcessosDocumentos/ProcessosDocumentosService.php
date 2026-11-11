<?php

declare(strict_types=1);

namespace App\Services\ProcessosDocumentos;

use App\DTOs\Admin\ProcessosDocumentosDTO;
use App\Enums\Admin\TiposDocumentosEnum;
use App\Models\Admin\ProcessosDocumentosModel;
use App\Services\Admin\LogsService;

class ProcessosDocumentosService implements IProcessosDocumentos
{
    public function __construct(
        protected ProcessosDocumentosModel $model,
        protected LogsService $logsService,
    ) {
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

    public function existeEditalAbertura(int $idProcesso, ?int $excetoId = null): bool
    {
        return $this->model->existeEditalAbertura($idProcesso, $excetoId);
    }

    /**
     * Para retificações, localiza o edital de abertura do processo, grava a data
     * de substituição no edital antigo e devolve um DTO com data de publicação
     * igual à data de substituição e vínculo ao edital retificado.
     */
    private function aplicarSubstituicaoEdital(ProcessosDocumentosDTO $dto): ProcessosDocumentosDTO
    {
        $edital = $this->model->buscarEditalAberturaPorProcesso($dto->processoReferencia);

        if (!$edital) {
            throw new \RuntimeException('Não existe Edital de Abertura para este processo. Cadastre-o antes de criar uma retificação.');
        }

        $dataAtual = date('Y-m-d H:i:s');
        $this->model->update((int) $edital->pk_id_documento, ['dt_data_substituicao' => $dataAtual]);

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
            versaoVigente:    0,
            documentoRetificado: (int) $edital->pk_id_documento,
            dataPublicacao:   $dataAtual,
            dataSubstituicao: null,
        );
    }
}
