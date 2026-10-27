<?php

declare(strict_types=1);

namespace App\Repositories\Admin\Interfaces;

/**
 * Contrato para operações de persistência de Contratos.
 */
interface IContratoRepository
{
    /**
     * Lista todos os contratos.
     */
    public function listarTodos(): array;

    /**
     * Busca contrato pelo ID da chave primária.
     */
    public function buscarPorId(int $id): ?object;

    /**
     * Busca contrato pelo ID com joins em dados relacionados.
     */
    public function buscarDetalhadoPorId(int $id): ?object;

    /**
     * Retorna contratos ativos com data de encerramento dentro do prazo.
     */
    public function buscarExpirando(int $prazoDias): array;

    /**
     * Retorna todos os contratos ativos com dados relacionados.
     */
    public function buscarAtivos(): array;

    /**
     * Retorna candidatos de um edital/curso que não possuem contrato ativo.
     */
    public function buscarPorEditalCurso(int $idEdital, int $idCurso): array;

    /**
     * Retorna todos os contratos de um candidato.
     */
    public function buscarPorCandidato(int $idCandidato): array;

    /**
     * Retorna contratos de um candidato filtrados por curso.
     */
    public function buscarPorCandidatoCurso(int $idCandidato, int $idCurso): array;

    /**
     * Insere um novo contrato.
     */
    public function inserir(array $dados): bool;

    /**
     * Atualiza um contrato existente.
     */
    public function atualizar(int $id, array $dados): bool;

    /**
     * Remove um contrato.
     */
    public function deletar(int $id): bool;

    /**
     * Retorna o número de linhas afetadas na última operação.
     */
    public function linhasAfetadas(): int;

    /**
     * Retorna o último ID inserido.
     */
    public function obterUltimoId(): int;
}
