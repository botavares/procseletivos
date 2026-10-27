<?php

declare(strict_types=1);

namespace App\Services\ProcessosDocumentos;

use App\DTOs\Admin\ProcessosDocumentosDTO;

interface IProcessosDocumentos
{
    public function criar(ProcessosDocumentosDTO $dto): int;
    public function atualizar(int $id, ProcessosDocumentosDTO $dto): bool;
    public function deletar(int $id): bool;
    public function buscarPorId(int $id): ?object;
    public function listarTodos(): array;
    public function listarPorSecretaria(int $idSecretaria): array;
    public function listarPorTipo(int $tipo): array;
    public function listarPorNumeroProcesso(int $numero, int $ano): array;
    public function listarPorNumeroDocumentoEAno(int $numero, int $ano): array;
    public function listarPorFaixaDeTempo(string $dataInicio, string $dataFim): array;
}
