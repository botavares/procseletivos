<?php

declare(strict_types=1);

namespace App\Services\ProcessosSeletivos;

use App\DTOs\Admin\ProcessosSeletivosDTO;

interface IProcessosSeletivos
{
    /**
     * Cria um novo processo seletivo.
     */
    public function criar(ProcessosSeletivosDTO $dto): int;

    /**
     * Atualiza um processo seletivo existente.
     */
    public function atualizar(int $id, ProcessosSeletivosDTO $dto): bool;

    /**
     * Realiza soft delete de um processo seletivo.
     */
    public function deletar(int $id): bool;

    /**
     * Busca um processo seletivo pelo ID.
     */
    public function buscarPorId(int $id): ?object;

    /**
     * Lista todos os processos seletivos não deletados.
     */
    public function listarTodos(): array;

    /**
     * Lista processos seletivos por secretaria.
     */
    public function listarPorSecretaria(int $idSecretaria): array;

    /**
     * Lista processos seletivos por ano.
     */
    public function listarPorAno(int $ano): array;

    /**
     * Busca processo seletivo por número e ano.
     */
    public function buscarPorNumeroEAno(int $numero, int $ano): ?object;

    /**
     * Lista processos seletivos por situação.
     */
    public function listarPorSituacao(int $situacao): array;

    /**
     * Lista processos seletivos por faixa de tempo de inscrição.
     */
    public function listarPorFaixaDeTempo(string $dataInicio, string $dataFim): array;

    /**
     * Lista processos seletivos com situação, excluindo vigentes e encerrados.
     * Útil para exibição na home pública.
     */
    public function listarAtivosComSituacao(): array;

    /**
     * Busca cargos vinculados a um processo seletivo.
     */
    public function buscarCargosPorProcesso(int $processoId): array;
}
