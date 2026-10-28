<?php

declare(strict_types=1);

namespace App\Services\ProcessosDocumentos;

use App\DTOs\Admin\ProcessosDocumentosDTO;
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
}
