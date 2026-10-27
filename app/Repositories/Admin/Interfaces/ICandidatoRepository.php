<?php

declare(strict_types=1);

namespace App\Repositories\Admin\Interfaces;

/**
 * Contrato para operações de persistência de Candidatos.
 */
interface ICandidatoRepository
{
    /**
     * Lista candidatos inscritos em um edital/cargo.
     */
    public function buscarPorEditalCargo(int $idEdital, int $idCargo): array;

    /**
     * Lista candidatos que possuem recursos protocolados para o edital/cargo.
     */
    public function buscarComRecursosPorEditalCargo(int $idEdital, int $idCargo): array;

    /**
     * Busca candidato pelo CPF com dados acadêmicos.
     */
    public function buscarPorCpf(string $cpf): ?object;

    /**
     * Busca candidato detalhado pelo ID.
     */
    public function buscarPorId(int $edital, int $cargo, int $id): ?object;

    /**
     * Lista experiências profissionais do candidato.
     */
    public function buscarExperiencias(int $edital, int $cargo, int $id): array;

    /**
     * Lista escolaridades do candidato.
     */
    public function buscarEscolaridades(int $edital, int $cargo, int $id): array;

    /**
     * Lista cursos de aperfeiçoamento do candidato.
     */
    public function buscarAperfeicoamentos(int $edital, int $cargo, int $id): array;

    /**
     * Lista critérios adicionais do candidato.
     */
    public function buscarCriterios(int $edital, int $cargo, int $id): array;

    /**
     * Retorna a quantidade total de candidatos cadastrados no ano.
     */
    public function contarPorAno(int $ano): int;
}
