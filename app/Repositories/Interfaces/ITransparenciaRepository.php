<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface ITransparenciaRepository
{
    /**
     * Busca editais ativos
     */
    public function buscarEditaisAtivos(): array;

    /**
     * Lista classificação com filtros aplicados
     */
    public function listarClassificacao(
        int $pagina,
        int $porPagina,
        int $cargo,
        int $edital,
        string $busca,
        array $editaisAtivos,
        bool $usaDesempateDinamico,
        array $colunasDinamicas,
        array $criteriosAdicionais
    ): array;

    /**
     * Busca colunas dinâmicas (scores) para o cargo/edital
     */
    public function buscarColunasDinamicas(int $cargo, int $edital, string $busca, array $editaisAtivos): array;

    /**
     * Carrega nomes das referências do cargo
     */
    public function carregarNomesReferencias(int $cargo): array;

    /**
     * Verifica quais colunas de pontuação estão vazias
     */
    public function obterColunasOcultas(int $cargo, int $edital, string $busca, array $editaisAtivos): array;

    /**
     * Busca critérios adicionais ativos para o cargo/edital
     */
    public function obterCriteriosAdicionaisAtivos(int $cargo, int $edital, string $busca, array $editaisAtivos): array;
}
