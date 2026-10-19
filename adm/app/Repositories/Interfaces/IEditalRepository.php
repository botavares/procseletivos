<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

/**
 * Contrato para operações de persistência de Editais.
 */
interface IEditalRepository
{
    /**
     * Lista todos os editais.
     */
    public function listarTodos(): array;

    /**
     * Busca edital pelo ID.
     */
    public function buscarPorId(int $id): ?object;

    /**
     * Lista editais ativos (status = 1).
     */
    public function listarAtivos(): array;

    /**
     * Salva um novo edital.
     */
    public function salvar(array $dados): bool;

    /**
     * Retorna o último ID inserido.
     */
    public function obterUltimoId(): int;

    /**
     * Atualiza um edital existente.
     */
    public function atualizar(int $id, array $dados): bool;

    /**
     * Remove um edital.
     */
    public function deletar(int $id): bool;

    /**
     * Retorna cargos dos editais ativos com contagem de candidatos.
     */
    public function buscarCargosComContagem(): array;

    /**
     * Retorna erros do model após uma operação.
     */
    public function obterErros(): array;
}
